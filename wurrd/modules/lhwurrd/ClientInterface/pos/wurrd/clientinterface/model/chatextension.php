<?php

$def = new ezcPersistentObjectDefinition();
$def->table = "wci_chat_extension";
$def->class = "Wurrd\\ClientInterface\\Model\\ChatExtension";

$def->idProperty = new ezcPersistentObjectIdProperty();
$def->idProperty->columnName = 'id';
$def->idProperty->propertyName = 'id';
$def->idProperty->generator = new ezcPersistentGeneratorDefinition(  'ezcPersistentNativeGenerator' );

$def->properties['chatid'] = new ezcPersistentObjectProperty();
$def->properties['chatid']->columnName   = 'chatid';
$def->properties['chatid']->propertyName = 'chatid';
$def->properties['chatid']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;

$def->properties['revision'] = new ezcPersistentObjectProperty();
$def->properties['revision']->columnName   = 'revision';
$def->properties['revision']->propertyName = 'revision';
$def->properties['revision']->propertyType = ezcPersistentObjectProperty::PHP_TYPE_INT;


return $def;


?>