<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "waa_authorization";
$def->class = "Wurrd\\ClientInterface\\Model\\Authorization";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'authid';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['operatorid'] = new ezcPersistentObjectProperty();
$def->properties['operatorid']->columnName   = 'operatorid';
$def->properties['operatorid']->propertyName = 'operatorid';
$def->properties['operatorid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['deviceid'] = new ezcPersistentObjectProperty();
$def->properties['deviceid']->columnName   = 'deviceid';
$def->properties['deviceid']->propertyName = 'deviceid';
$def->properties['deviceid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['clientid'] = new ezcPersistentObjectProperty();
$def->properties['clientid']->columnName   = 'clientid';
$def->properties['clientid']->propertyName = 'clientid';
$def->properties['clientid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dtmcreated'] = new ezcPersistentObjectProperty();
$def->properties['dtmcreated']->columnName   = 'dtmcreated';
$def->properties['dtmcreated']->propertyName = 'dtmcreated';
$def->properties['dtmcreated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dtmmodified'] = new ezcPersistentObjectProperty();
$def->properties['dtmmodified']->columnName   = 'dtmmodified';
$def->properties['dtmmodified']->propertyName = 'dtmmodified';
$def->properties['dtmmodified']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['accesstoken'] = new ezcPersistentObjectProperty();
$def->properties['accesstoken']->columnName   = 'accesstoken';
$def->properties['accesstoken']->propertyName = 'accesstoken';
$def->properties['accesstoken']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dtmaccesscreated'] = new ezcPersistentObjectProperty();
$def->properties['dtmaccesscreated']->columnName   = 'dtmaccesscreated';
$def->properties['dtmaccesscreated']->propertyName = 'dtmaccesscreated';
$def->properties['dtmaccesscreated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dtmaccessexpires'] = new ezcPersistentObjectProperty();
$def->properties['dtmaccessexpires']->columnName   = 'dtmaccessexpires';
$def->properties['dtmaccessexpires']->propertyName = 'dtmaccessexpires';
$def->properties['dtmaccessexpires']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['refreshtoken'] = new ezcPersistentObjectProperty();
$def->properties['refreshtoken']->columnName   = 'refreshtoken';
$def->properties['refreshtoken']->propertyName = 'refreshtoken';
$def->properties['refreshtoken']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dtmrefreshcreated'] = new ezcPersistentObjectProperty();
$def->properties['dtmrefreshcreated']->columnName   = 'dtmrefreshcreated';
$def->properties['dtmrefreshcreated']->propertyName = 'dtmrefreshcreated';
$def->properties['dtmrefreshcreated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dtmrefreshexpires'] = new ezcPersistentObjectProperty();
$def->properties['dtmrefreshexpires']->columnName   = 'dtmrefreshexpires';
$def->properties['dtmrefreshexpires']->propertyName = 'dtmrefreshexpires';
$def->properties['dtmrefreshexpires']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['previousaccesstoken'] = new ezcPersistentObjectProperty();
$def->properties['previousaccesstoken']->columnName   = 'previousaccesstoken';
$def->properties['previousaccesstoken']->propertyName = 'previousaccesstoken';
$def->properties['previousaccesstoken']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['previousrefreshtoken'] = new ezcPersistentObjectProperty();
$def->properties['previousrefreshtoken']->columnName   = 'previousrefreshtoken';
$def->properties['previousrefreshtoken']->propertyName = 'previousrefreshtoken';
$def->properties['previousrefreshtoken']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;



return $def;


?>