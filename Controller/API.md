# Pi-Stash

## Resources
- file
- folder
- system
- apps

## Filesystem
-GET    /file?path=     --> Gets a file by path
returns {
    "type": "file",
    "attributes": {
        "name": basename($path),
        "path": $path,
        "extension": pathinfo($path, PATHINFO_EXTENSION),
        "download": "http:\\$_SERVER['SERVER_NAME']\\$_ENV['BASENAME']\\api\\download\\$path"
    }
}


-POST   /file?path=     --> Creates a file with path
-PUT
-DELETE