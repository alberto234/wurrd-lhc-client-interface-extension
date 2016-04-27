<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "waa_device";
$def->class = "Wurrd\\ClientInterface\\Model\\Device";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['deviceuuid'] = new ezcPersistentObjectProperty();
$def->properties['deviceuuid']->columnName   = 'deviceuuid';
$def->properties['deviceuuid']->propertyName = 'deviceuuid';
$def->properties['deviceuuid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['platform'] = new ezcPersistentObjectProperty();
$def->properties['platform']->columnName   = 'platform';
$def->properties['platform']->propertyName = 'platform';
$def->properties['platform']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['type'] = new ezcPersistentObjectProperty();
$def->properties['type']->columnName   = 'type';
$def->properties['type']->propertyName = 'type';
$def->properties['type']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['name'] = new ezcPersistentObjectProperty();
$def->properties['name']->columnName   = 'name';
$def->properties['name']->propertyName = 'name';
$def->properties['name']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['os'] = new ezcPersistentObjectProperty();
$def->properties['os']->columnName   = 'os';
$def->properties['os']->propertyName = 'os';
$def->properties['os']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['osversion'] = new ezcPersistentObjectProperty();
$def->properties['osversion']->columnName   = 'osversion';
$def->properties['osversion']->propertyName = 'osversion';
$def->properties['osversion']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_STRING;

$def->properties['dtmcreated'] = new ezcPersistentObjectProperty();
$def->properties['dtmcreated']->columnName   = 'dtmcreated';
$def->properties['dtmcreated']->propertyName = 'dtmcreated';
$def->properties['dtmcreated']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['dtmmodified'] = new ezcPersistentObjectProperty();
$def->properties['dtmmodified']->columnName   = 'dtmmodified';
$def->properties['dtmmodified']->propertyName = 'dtmmodified';
$def->properties['dtmmodified']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;


return $def;


?>