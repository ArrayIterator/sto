<?php
/**
 * Routes API Configurations
 */
namespace ArrayIterator\Routes;

use ArrayIterator\Controller\Api\ClassesController;
use ArrayIterator\Controller\Api\ModuleController;
use ArrayIterator\Controller\Api\StatusController;
use ArrayIterator\Controller\Api\SupervisorController;

return [
    StatusController::class,
    ClassesController::class,
    ModuleController::class,
    SupervisorController::class,
];

