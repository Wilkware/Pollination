<?php

/**
 * ProfileHelper.php
 *
 * Part of the Trait-Libraray for IP-Symcon Modules.
 *
 * @package       traits
 * @author        Heiko Wilknitz <heiko@wilkware.de>
 * @copyright     2025 Heiko Wilknitz
 * @link          https://wilkware.de
 * @license       https://creativecommons.org/licenses/by-nc-sa/4.0/ CC BY-NC-SA 4.0
 */

declare(strict_types=1);

/**
 * Helper class for create variable profiles.
 */
trait ProfileHelper
{
    /**
     * Create the profile for the given type with the passed name.
     *
     * @param string $name    Profil name.
     * @param int    $vartype Type of the variable.
     *
     * @return void
     */
    protected function RegisterProfileType(string $name, int $vartype): void
    {
        if (!IPS_VariableProfileExists($name)) {
            IPS_CreateVariableProfile($name, $vartype);
        } else {
            $profile = IPS_GetVariableProfile($name);
            if ($profile['ProfileType'] != $vartype) {
                throw new Exception('Variable profile type does not match for profile ' . $name);
            }
        }
    }

    /**
     * Create a profile for boolean values.
     *
     * @param string $name   Profil name.
     * @param string $icon   Icon to display.
     * @param string $prefix Variable prefix.
     * @param string $suffix Variable suffix.
     * @param array<int,array{0:bool,1:string,2:string,3:int}> $asso Associations of the values.
     *
     * @return void
     */
    protected function RegisterProfileBoolean(string $name, string $icon, string $prefix, string $suffix, array $asso = null): void
    {
        $this->RegisterProfileType($name, VARIABLETYPE_BOOLEAN);

        IPS_SetVariableProfileIcon($name, $icon);
        IPS_SetVariableProfileText($name, $prefix, $suffix);

        if (($asso !== null) && (count($asso) !== 0)) {
            foreach ($asso as $ass) {
                IPS_SetVariableProfileAssociation($name, $ass[0], $this->Translate($ass[1]), $ass[2], $ass[3]);
            }
        }
    }

    /**
     * Create a profile for integer values.
     *
     * @param string $name      Profil name.
     * @param string $icon      Icon to display.
     * @param string $prefix    Variable prefix.
     * @param string $suffix    Variable suffix.
     * @param int    $minvalue  Minimum value.
     * @param int    $maxvalue  Maximum value.
     * @param int    $stepsize  Increment.
     * @param array<int,array{0:int,1:string,2:string,3:int}> $asso Associations of the values.
     *
     * @return void
     */
    protected function RegisterProfileInteger(string $name, string $icon, string $prefix, string $suffix, int $minvalue, int $maxvalue, int $stepsize, array $asso = null): void
    {
        $this->RegisterProfileType($name, VARIABLETYPE_INTEGER);

        IPS_SetVariableProfileIcon($name, $icon);
        IPS_SetVariableProfileText($name, $prefix, $suffix);
        IPS_SetVariableProfileValues($name, $minvalue, $maxvalue, $stepsize);

        if (($asso !== null) && (count($asso) !== 0)) {
            foreach ($asso as $ass) {
                IPS_SetVariableProfileAssociation($name, $ass[0], $this->Translate($ass[1]), $ass[2], $ass[3]);
            }
        }
    }

    /**
     * Create a profile for float values.
     *
     * @param string $name     Profil name.
     * @param string $icon     Icon to display.
     * @param string $prefix   Variable prefix.
     * @param string $suffix   Variable suffix.
     * @param int    $minvalue Minimum value.
     * @param int    $maxvalue Maximum value.
     * @param int    $stepsize Increment.
     * @param int    $digits   Decimal places.
     * @param array<int,array{0:float,1:string,2:string,3:int}> $asso Associations of the values.
     *
     * @return void
     */
    protected function RegisterProfileFloat(string $name, string $icon, string $prefix, string $suffix, int $minvalue, int $maxvalue, int $stepsize, int $digits, array $asso = null): void
    {
        $this->RegisterProfileType($name, VARIABLETYPE_FLOAT);

        IPS_SetVariableProfileIcon($name, $icon);
        IPS_SetVariableProfileText($name, $prefix, $suffix);
        IPS_SetVariableProfileValues($name, $minvalue, $maxvalue, $stepsize);
        IPS_SetVariableProfileDigits($name, $digits);

        if (($asso !== null) && (count($asso) !== 0)) {
            foreach ($asso as $ass) {
                IPS_SetVariableProfileAssociation($name, $ass[0], $this->Translate($ass[1]), $ass[2], $ass[3]);
            }
        }
    }

    /**
     * Create a profile for string values.
     *
     * @param string $name   Profil name.
     * @param string $icon   Icon to display.
     * @param string $prefix Variable prefix.
     * @param string $suffix Variable suffix.
     * @param array<int,array{0:string,1:string,2:string,3:int}> $asso Associations of the values.
     *
     * @return void
     */
    protected function RegisterProfileString(string $name, string $icon, string $prefix, string $suffix, array $asso): void
    {
        $this->RegisterProfileType($name, VARIABLETYPE_STRING);

        IPS_SetVariableProfileIcon($name, $icon);
        IPS_SetVariableProfileText($name, $prefix, $suffix);

        if (count($asso) !== 0) {
            foreach ($asso as $ass) {
                IPS_SetVariableProfileAssociation($name, $ass[0], $this->Translate($ass[1]), $ass[2], $ass[3]);
            }
        }
    }

    /**
     * Returns the used profile name of a variable
     *
     * @param int $id Variable ID
     *
     * @return string Empty, or name of the profile
     */
    protected function GetVariableProfile(int $id): string
    {
        $variableProfileName = IPS_GetVariable($id)['VariableCustomProfile'];
        if ($variableProfileName == '') {
            $variableProfileName = IPS_GetVariable($id)['VariableProfile'];
        }
        return $variableProfileName;
    }
}