#Assets Extension

Installation
------------

1. In [config.neon](./../../config/config.neon) add extension [AssetsExtension.php](./AssetsExtension.php)

```neon
extensions:
    assets: App\Utils\Assets\AssetsExtension
```

2. Add Macros
```neon
latte:
    macros:
        - App\Utils\Assets\Macros
```

3. Configure assets. For Example

Local Development configuration
```neon
assets:
    publicPath: /output/dev
    devServer:  http://localhost:8080/
```
Development configuration
```neon
assets:
    publicPath: /output/dev
```
Production configuration
```neon
assets:
    publicPath: /output/prod
```

Default configuration
```neon
assets:
    debugMode: %debugMode%
    wwwDir: %wwwDir%
    publicPath: null
    devServer:  null
    manifest: manifest.json
```

Usage
------------
```html
<script src="{assets admin.js}"></script>
```
