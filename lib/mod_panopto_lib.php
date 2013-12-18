<?php
/* Copyright Panopto 2009 - 2013 / With contributions from Spenser Jones (sjones@ambrose.edu) and Jay Briers (jay@jrbriers.co.uk)
 * 
 * This file is part of the Panopto Activity Module for Moodle adapted from the Panopto Block for Moodle.
 * 
 * The Panopto Activity Module for Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * The Panopto plugin for Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with the Panopto plugin for Moodle.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 *
 * @package    mod
 * @subpackage panopto
 * @copyright  2013 onwards Jay Briers, Lancaster University {@link http://jrbriers.co.uk}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Prepend the instance name to the Moodle course ID to create an external ID for Panopto Focus.
function mod_panopto_decorate_course_id($moodle_course_id) {
    return (INSTANCE_NAME . ":" . $moodle_course_id);
}

// Decorate a moodle username with the instancename outside the context of a panopto_data object.
function mod_panopto_decorate_username($moodle_username) {
    return (INSTANCE_NAME . "\\" . $moodle_username);
}

// Sign the payload with the proof that it was generated by trusted code.
function mod_panopto_generate_auth_code($payload) {
    $sharedSecret = APIKEY;

    $signed_payload = $payload . "|" . $sharedSecret;

    $auth_code = sha1($signed_payload);
    $auth_code = strtoupper($auth_code);

    return $auth_code;
}

function mod_panopto_validate_auth_code($payload, $auth_code) {
    return (mod_panopto_generate_auth_code($payload) == $auth_code);
}
/* End of file block_panopto_lib.php */