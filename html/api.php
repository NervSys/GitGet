<?php

/**
 * API Entry
 *
 * Copyright 2016-2019 Jerry Shaw <jerry-shaw@live.com>
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

//Declare strict types
declare(strict_types=1);

//Define root path
define('ROOT', realpath(__DIR__ . '/..') . '/');

//Define entry path
define('ENTRY_SCRIPT', __FILE__);

//Load system script
require ROOT . 'NervSys/core/system.php';

//Boot system
\core\system::boot();