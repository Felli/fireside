<?php
// Copyright 2011 Toby Zerner, Simon Zerner
// This file is part of esoTalk. Please see the included license file for usage information.

if (!defined("IN_ESOTALK")) exit;

/**
 * The FSForm class defines a form which can be rendered as HTML in the view and its input processed in the
 * controller.
 *
 * Many of the functions in this class are for rendering form controls, and are intended to be used in the
 * view. Generally, a controller will instantiate the form, define its default values and its action, check
 * if it has been posted back, carry out any necessary processing and error checking, and then pass the form
 * onto the view. The view will render the form elements individually.
 *
 * However, it also contains functions to define a form's structure and contents in the controller rather than
 * on the view. This is useful as it gives plugins the opportunity to alter forms and add custom fields in
 * any position. See addSection() and addField() for more information on this.
 *
 * @package esoTalk
 */
class FSForm extends ETPluggable {


/**
 * An array of "fields" in the form. Essentially defines the structure of the form.
 * @var array
 */
public $sections = array();


/**
 * An array of errors to show when the form is rendered.
 * @var array
 */
public $errors = array();


/**
 * The "action" attribute of the <form> tag.
 * @var string
 */
public $action = "";


/**
 * An array of default values for the form fields.
 * @var array
 */
public $values = array();


/**
 * An array of hidden inputs to render when the form is opened.
 * @var array
 */
public $hiddenInputs = array();


/**
 * Add a section to the form
 *
 * @param string $id
 * @param string $title
 * @param mixed $fields
 * @return void
 */
public function section($id, $title, $fields = Array()) {
    // Determine if section exists
    $sectionExists = array_reduce($this->sections, function($carry, $item) use ($id) {
        if ($item["id"] == $id) {
            $carry = true;
        }
    }, false);

    // If we need to make a new section
    if(!$sectionExists) {

        /// If fields are not passed in, set initial fields to empty array
        $initialFields = isset($fields)? $fields : Array();

        // Add section to object
        array_push($this->sections, Array(
            "id" => $id,
            "title" => $title,
            "fields" => $initialFields
        ));
    }
}


/**
 * Add a field to the form.
 *
 * @param string $section The name of the section to add this field to.
 * @param string $id The name of the field.
 * @param mixed $renderCallback The function to call that will return the field's HTML.
 * @param mixed $processCallback The function to call that will process the field's input.
 * @param mixed $position The position to put this field relative to other fields.
 * @return void
 */
public function addToSection($sectionId, $fields)
{
    // Iterate over all sections for the form
    foreach ($this->sections as &$section) {
        // If the section ID matches, add the given fields to the section
        if ($section["id"] == $sectionId) {
            $section["fields"] = array_merge($section["fields"], $fields);
        }
    }
}


/**
 * Add a hidden field to the form.
 *
 * @param string $name The name of the hidden form field
 * @param string $value Optionally, a value to use as the form field value
 * @return void
 */
public function hidden($name, $value = null) {
    $field = Array( "name" => $name);
    if(isset($value)) {
        $field["value"] = $value;
    }
    array_push($this->hiddenInputs, $field);
}


/**
 * Get a text field
 *
 * @param string $name The name of the hidden form field
 * @param mixed $id Optionally, a value to use as the form field value
 * @return void
 */
public static function text($name, $options) {
    $field = Array( "name" => $name, "type" => "text" );
    if(isset($options)) {
        $field = array_merge($field, $options);
    }
    return $field;
}


/**
 * Get a text field
 *
 * @param string $name The name of the hidden form field
 * @param mixed $id Optionally, a value to use as the form field value
 * @return void
 */
public static function password($name, $options) {
    $field = Array( "name" => $name, "type" => "password" );
    if(isset($options)) {
        $field = array_merge($field, $options);
    }
    return $field;
}


/**
 * Checks if the form has been posted back and if a valid token was posted back with it.
 *
 * @param string $field An optional field to check the existence of.
 * @return bool
 */
public function validPostBack($field = "")
{
	return $this->isPostBack($field) and ET::$session->validateToken(@$_POST["token"]);
}


/**
 * Checks if the form has been posted back. Does not require a valid token to be posted back as well.
 *
 * @param string $field An optional field to check the existence of.
 * @return bool
 */
public static function isPostBack($field = "")
{
	return $field ? isset($_POST[$field]) : !empty($_POST);
}


/**
 * Get the values of all fields posted back.
 *
 * @return array
 */
public function values()
{
    $input = array();
    foreach ($this->sections as &$section) {
        foreach ($section["fields"] as &$field) {
            // If the section ID matches, add the given fields to the section
            if (FSForm::isPostBack($field["name"])) {
                $input[$field["name"]] = $_POST[$field["name"]];
            }
        }
    }
	return $input;
}


/**
 * Set an error on a specific field in the form.
 *
 * @param string $field The name of the field to set the error on.
 * @param string $message The error message.
 * @return void
 */
public function error($field, $message)
{
	$this->errors[$field] = $message;
}


/**
 * Set errors on multiple fields using an array.
 *
 * @param array $errors An array of errors with field name => error message elements.
 * @return void
 */
public function errors($errors)
{
	foreach ($errors as $k => $v) $this->error($k, T("message.$v"));
}


}
