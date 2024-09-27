<?php
namespace Src\TableGateways;

$personGateway = new PersonGateway($dbConnection);


// return all records
$result = $personGateway->findAll();

// return the record with id = 1
$result = $personGateway->find(1);

// insert a new record
$result = $personGateway->insert([
    'firstname' => 'Doug',
    'lastname' => 'Ellis'
]);

// update the record with id = 10
$result = $personGateway->update(10, [
    'firstname' => 'Doug',
    'lastname' => 'Ellis',
    'secondparent_id' => 1
]);

// delete the record with id = 10
$result = $personGateway->delete(10);