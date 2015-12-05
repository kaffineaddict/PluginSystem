# PluginSystem
A CakePHP hooks component for basic user extensions

## Installation

Run the following
```
composer require KaffineAddict/plugin-system:dev-master
```

Add the following to bootstrap
```
Plugin::load('PluginSystem', ['bootstrap' => false, 'routes' => true]);
```

Run the following
```
.bin/cake migrations migrate -p PluginSystem
```
