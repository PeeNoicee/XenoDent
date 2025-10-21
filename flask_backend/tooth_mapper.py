class ToothPositionMapper:
    """
    Maps pixel coordinates to dental quadrants and tooth positions
    for panoramic X-ray images
    """
    
    def __init__(self):
        self.tooth_names = {
            # Upper Right (Quadrant 1)
            11: 'Upper Right Central Incisor',
            12: 'Upper Right Lateral Incisor',
            13: 'Upper Right Canine',
            14: 'Upper Right First Premolar',
            15: 'Upper Right Second Premolar',
            16: 'Upper Right First Molar',
            17: 'Upper Right Second Molar',
            18: 'Upper Right Third Molar',
            
            # Upper Left (Quadrant 2)
            21: 'Upper Left Central Incisor',
            22: 'Upper Left Lateral Incisor',
            23: 'Upper Left Canine',
            24: 'Upper Left First Premolar',
            25: 'Upper Left Second Premolar',
            26: 'Upper Left First Molar',
            27: 'Upper Left Second Molar',
            28: 'Upper Left Third Molar',
            
            # Lower Left (Quadrant 3)
            31: 'Lower Left Central Incisor',
            32: 'Lower Left Lateral Incisor',
            33: 'Lower Left Canine',
            34: 'Lower Left First Premolar',
            35: 'Lower Left Second Premolar',
            36: 'Lower Left First Molar',
            37: 'Lower Left Second Molar',
            38: 'Lower Left Third Molar',
            
            # Lower Right (Quadrant 4)
            41: 'Lower Right Central Incisor',
            42: 'Lower Right Lateral Incisor',
            43: 'Lower Right Canine',
            44: 'Lower Right First Premolar',
            45: 'Lower Right Second Premolar',
            46: 'Lower Right First Molar',
            47: 'Lower Right Second Molar',
            48: 'Lower Right Third Molar',
        }
    
    def map_coordinates_to_tooth(self, x, y, image_width, image_height):
        """Map pixel coordinates to dental quadrant and tooth position"""
        
        # Normalize coordinates to percentages
        x_percent = (x / image_width) * 100
        y_percent = (y / image_height) * 100
        
        # Determine if upper or lower jaw
        is_upper = y_percent < 50  # Upper half of image
        
        # Determine left or right side
        is_right = x_percent < 50  # Left side of image (patient's right)
        
        # Determine quadrant
        if is_upper and is_right:
            quadrant = 'Upper Right'
            quadrant_number = 1
        elif is_upper and not is_right:
            quadrant = 'Upper Left'
            quadrant_number = 2
        elif not is_upper and not is_right:
            quadrant = 'Lower Left'
            quadrant_number = 3
        else:
            quadrant = 'Lower Right'
            quadrant_number = 4
        
        # Estimate tooth number based on horizontal position within quadrant
        tooth_number = self._estimate_tooth_number(x_percent, quadrant_number)
        tooth_name = self.tooth_names.get(tooth_number, 'Unknown Tooth')
        
        return {
            'quadrant': quadrant,
            'quadrant_number': quadrant_number,
            'tooth_number_fdi': tooth_number,
            'tooth_name': tooth_name,
            'position_description': self._get_position_description(x_percent, y_percent),
            'coordinates': {
                'x_percent': round(x_percent, 2),
                'y_percent': round(y_percent, 2),
                'x_pixel': x,
                'y_pixel': y
            }
        }
    
    def _estimate_tooth_number(self, x_percent, quadrant_number):
        """Estimate tooth number based on horizontal position"""
        
        # Adjust x position to quadrant-relative position
        if quadrant_number in [1, 4]:  # Right side quadrants
            quadrant_x = (50 - x_percent) / 50 * 100  # 0-100 from center to right
        else:  # Left side quadrants
            quadrant_x = (x_percent - 50) / 50 * 100  # 0-100 from center to left
        
        # Map to tooth positions (8 teeth per quadrant)
        if quadrant_x < 12.5:
            tooth_position = 1  # Central incisor
        elif quadrant_x < 25:
            tooth_position = 2  # Lateral incisor
        elif quadrant_x < 37.5:
            tooth_position = 3  # Canine
        elif quadrant_x < 50:
            tooth_position = 4  # First premolar
        elif quadrant_x < 62.5:
            tooth_position = 5  # Second premolar
        elif quadrant_x < 75:
            tooth_position = 6  # First molar
        elif quadrant_x < 87.5:
            tooth_position = 7  # Second molar
        else:
            tooth_position = 8  # Third molar (wisdom tooth)
        
        # Return FDI notation
        return (quadrant_number * 10) + tooth_position
    
    def _get_position_description(self, x_percent, y_percent):
        """Get position description based on coordinates"""
        
        # Horizontal position
        if x_percent < 25:
            horizontal = 'Far Right'
        elif x_percent < 45:
            horizontal = 'Right'
        elif x_percent < 55:
            horizontal = 'Center'
        elif x_percent < 75:
            horizontal = 'Left'
        else:
            horizontal = 'Far Left'
        
        # Vertical position
        if y_percent < 25:
            vertical = 'Upper'
        elif y_percent < 45:
            vertical = 'Upper-Mid'
        elif y_percent < 55:
            vertical = 'Mid'
        elif y_percent < 75:
            vertical = 'Lower-Mid'
        else:
            vertical = 'Lower'
        
        return f"{vertical} {horizontal}"
    
    def get_severity_from_size(self, width, height, image_width, image_height):
        """Get severity description based on bounding box size"""
        
        # Calculate relative size as percentage of image
        relative_width = (width / image_width) * 100
        relative_height = (height / image_height) * 100
        relative_area = relative_width * relative_height
        
        if relative_area < 0.5:
            return 'Small'
        elif relative_area < 2.0:
            return 'Moderate'
        elif relative_area < 5.0:
            return 'Large'
        else:
            return 'Extensive'
    
    def process_predictions(self, predictions, image_width, image_height):
        """Process multiple predictions and add dental position information"""
        
        processed_predictions = []
        
        for prediction in predictions:
            x = prediction['x']
            y = prediction['y']
            width = prediction['width']
            height = prediction['height']
            
            # Get tooth position information
            tooth_info = self.map_coordinates_to_tooth(x, y, image_width, image_height)
            
            # Get severity information
            severity = self.get_severity_from_size(width, height, image_width, image_height)
            
            # Create enhanced prediction
            enhanced_prediction = {
                'class': prediction['class'],
                'confidence': prediction['confidence'],
                'dental_location': {
                    'quadrant': tooth_info['quadrant'],
                    'tooth_number': tooth_info['tooth_number_fdi'],
                    'tooth_name': tooth_info['tooth_name'],
                    'position_description': tooth_info['position_description']
                },
                'severity': severity,
                'technical_details': {
                    'coordinates': tooth_info['coordinates'],
                    'bounding_box': {
                        'width': width,
                        'height': height,
                        'area_pixels': width * height
                    }
                },
                # Keep original data for backward compatibility
                'original_data': prediction
            }
            
            processed_predictions.append(enhanced_prediction)
        
        return processed_predictions
