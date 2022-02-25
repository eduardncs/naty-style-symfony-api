<?php

namespace App\Helper;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validator\ValidatorInterface;


trait ParameterTrait 
{
    /**
     * Check that the values from $checkVal are keys in $array
     *
     * @param array $array
     * @param array $checkVal
     * @return boolean
     */
    public function check_array(array $array, array $checkVal): bool
    {
        foreach($checkVal as $val) {
            if(!isset($array[$val]) || $array[$val] === null) {
                return false;
            }
        }

        return true;
    }

    /**
     * Validate input
     *
     * @param Collection $assert
     * @param array $input
     * @param ValidatorInterface $validator
     * @return array
     */
    public function validate_assert(Collection $assert, array $input, ValidatorInterface $validator): array 
    {
        $errorMsg = [];

        $violations = $validator->validate($input, $assert);

        if (count($violations) > 0) {
            $accessor = PropertyAccess::createPropertyAccessor();

            foreach ($violations as $violation) {
                $accessor->setValue($errorMsg,
                    $violation->getPropertyPath(),
                    $violation->getMessage());
            }
        }
        return $errorMsg;
    }
}