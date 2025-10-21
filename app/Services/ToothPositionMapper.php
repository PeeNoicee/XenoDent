<?php

namespace App\Services;

class ToothPositionMapper
{
    /**
     * Map pixel coordinates to dental quadrant and tooth position
     * Based on standard panoramic X-ray layout
     */
    public function mapCoordinatesToTooth($x, $y, $imageWidth, $imageHeight)
    {
        // Normalize coordinates to percentages
        $xPercent = ($x / $imageWidth) * 100;
        $yPercent = ($y / $imageHeight) * 100;
        
        // Determine if upper or lower jaw
        $isUpper = $yPercent < 50; // Upper half of image
        
        // Determine left or right side
        $isRight = $xPercent < 50; // Left side of image (patient's right)
        
        // Determine quadrant
        if ($isUpper && $isRight) {
            $quadrant = 'Upper Right';
            $quadrantNumber = 1;
        } elseif ($isUpper && !$isRight) {
            $quadrant = 'Upper Left';
            $quadrantNumber = 2;
        } elseif (!$isUpper && !$isRight) {
            $quadrant = 'Lower Left';
            $quadrantNumber = 3;
        } else {
            $quadrant = 'Lower Right';
            $quadrantNumber = 4;
        }
        
        // Estimate tooth number based on horizontal position within quadrant
        $toothNumber = $this->estimateToothNumber($xPercent, $quadrantNumber);
        $toothName = $this->getToothName($toothNumber);
        
        return [
            'quadrant' => $quadrant,
            'quadrant_number' => $quadrantNumber,
            'tooth_number_fdi' => $toothNumber,
            'tooth_name' => $toothName,
            'position_description' => $this->getPositionDescription($xPercent, $yPercent),
            'coordinates' => [
                'x_percent' => round($xPercent, 2),
                'y_percent' => round($yPercent, 2),
                'x_pixel' => $x,
                'y_pixel' => $y
            ]
        ];
    }
    
    /**
     * Estimate tooth number based on horizontal position
     */
    private function estimateToothNumber($xPercent, $quadrantNumber)
    {
        // Adjust x position to quadrant-relative position
        if ($quadrantNumber == 1 || $quadrantNumber == 4) {
            // Right side quadrants
            $quadrantX = (50 - $xPercent) / 50 * 100; // 0-100 from center to right
        } else {
            // Left side quadrants
            $quadrantX = ($xPercent - 50) / 50 * 100; // 0-100 from center to left
        }
        
        // Map to tooth positions (8 teeth per quadrant)
        // Central incisors are at center, molars at edges
        if ($quadrantX < 12.5) {
            $toothPosition = 1; // Central incisor
        } elseif ($quadrantX < 25) {
            $toothPosition = 2; // Lateral incisor
        } elseif ($quadrantX < 37.5) {
            $toothPosition = 3; // Canine
        } elseif ($quadrantX < 50) {
            $toothPosition = 4; // First premolar
        } elseif ($quadrantX < 62.5) {
            $toothPosition = 5; // Second premolar
        } elseif ($quadrantX < 75) {
            $toothPosition = 6; // First molar
        } elseif ($quadrantX < 87.5) {
            $toothPosition = 7; // Second molar
        } else {
            $toothPosition = 8; // Third molar (wisdom tooth)
        }
        
        // Return FDI notation
        return ($quadrantNumber * 10) + $toothPosition;
    }
    
    /**
     * Get tooth name from FDI number
     */
    private function getToothName($fdiNumber)
    {
        $toothNames = [
            // Upper Right (Quadrant 1)
            11 => 'Upper Right Central Incisor',
            12 => 'Upper Right Lateral Incisor',
            13 => 'Upper Right Canine',
            14 => 'Upper Right First Premolar',
            15 => 'Upper Right Second Premolar',
            16 => 'Upper Right First Molar',
            17 => 'Upper Right Second Molar',
            18 => 'Upper Right Third Molar',
            
            // Upper Left (Quadrant 2)
            21 => 'Upper Left Central Incisor',
            22 => 'Upper Left Lateral Incisor',
            23 => 'Upper Left Canine',
            24 => 'Upper Left First Premolar',
            25 => 'Upper Left Second Premolar',
            26 => 'Upper Left First Molar',
            27 => 'Upper Left Second Molar',
            28 => 'Upper Left Third Molar',
            
            // Lower Left (Quadrant 3)
            31 => 'Lower Left Central Incisor',
            32 => 'Lower Left Lateral Incisor',
            33 => 'Lower Left Canine',
            34 => 'Lower Left First Premolar',
            35 => 'Lower Left Second Premolar',
            36 => 'Lower Left First Molar',
            37 => 'Lower Left Second Molar',
            38 => 'Lower Left Third Molar',
            
            // Lower Right (Quadrant 4)
            41 => 'Lower Right Central Incisor',
            42 => 'Lower Right Lateral Incisor',
            43 => 'Lower Right Canine',
            44 => 'Lower Right First Premolar',
            45 => 'Lower Right Second Premolar',
            46 => 'Lower Right First Molar',
            47 => 'Lower Right Second Molar',
            48 => 'Lower Right Third Molar',
        ];
        
        return $toothNames[$fdiNumber] ?? 'Unknown Tooth';
    }
    
    /**
     * Get position description based on coordinates
     */
    private function getPositionDescription($xPercent, $yPercent)
    {
        $horizontal = '';
        $vertical = '';
        
        // Horizontal position
        if ($xPercent < 25) {
            $horizontal = 'Far Right';
        } elseif ($xPercent < 45) {
            $horizontal = 'Right';
        } elseif ($xPercent < 55) {
            $horizontal = 'Center';
        } elseif ($xPercent < 75) {
            $horizontal = 'Left';
        } else {
            $horizontal = 'Far Left';
        }
        
        // Vertical position
        if ($yPercent < 25) {
            $vertical = 'Upper';
        } elseif ($yPercent < 45) {
            $vertical = 'Upper-Mid';
        } elseif ($yPercent < 55) {
            $vertical = 'Mid';
        } elseif ($yPercent < 75) {
            $vertical = 'Lower-Mid';
        } else {
            $vertical = 'Lower';
        }
        
        return $vertical . ' ' . $horizontal;
    }
    
    /**
     * Get severity description based on bounding box size
     */
    public function getSeverityFromSize($width, $height, $imageWidth, $imageHeight)
    {
        // Calculate relative size as percentage of image
        $relativeWidth = ($width / $imageWidth) * 100;
        $relativeHeight = ($height / $imageHeight) * 100;
        $relativeArea = $relativeWidth * $relativeHeight;
        
        if ($relativeArea < 0.5) {
            return 'Small';
        } elseif ($relativeArea < 2.0) {
            return 'Moderate';
        } elseif ($relativeArea < 5.0) {
            return 'Large';
        } else {
            return 'Extensive';
        }
    }
    
    /**
     * Process multiple predictions and add dental position information
     */
    public function processPredictions($predictions, $imageWidth, $imageHeight)
    {
        $processedPredictions = [];
        
        foreach ($predictions as $prediction) {
            $x = $prediction['x'];
            $y = $prediction['y'];
            $width = $prediction['width'];
            $height = $prediction['height'];
            
            // Get tooth position information
            $toothInfo = $this->mapCoordinatesToTooth($x, $y, $imageWidth, $imageHeight);
            
            // Get severity information
            $severity = $this->getSeverityFromSize($width, $height, $imageWidth, $imageHeight);
            
            // Create enhanced prediction
            $enhancedPrediction = [
                'class' => $prediction['class'],
                'confidence' => $prediction['confidence'],
                'dental_location' => [
                    'quadrant' => $toothInfo['quadrant'],
                    'tooth_number' => $toothInfo['tooth_number_fdi'],
                    'tooth_name' => $toothInfo['tooth_name'],
                    'position_description' => $toothInfo['position_description']
                ],
                'severity' => $severity,
                'technical_details' => [
                    'coordinates' => $toothInfo['coordinates'],
                    'bounding_box' => [
                        'width' => $width,
                        'height' => $height,
                        'area_pixels' => $width * $height
                    ]
                ],
                // Keep original data for backward compatibility
                'original_data' => $prediction
            ];
            
            $processedPredictions[] = $enhancedPrediction;
        }
        
        return $processedPredictions;
    }
}
