# PetWatcher-API :dog::cat::rabbit::parrot:
[![Build Status](https://img.shields.io/github/workflow/status/DominicLuidold/PetWatcher-API/CI/develop)](https://github.com/DominicLuidold/PetWatcher-API/actions)
[![Minimum PHP Version](https://img.shields.io/badge/php-%3E%3D7.3-informational)](https://php.net/)
[![License](https://img.shields.io/github/license/DominicLuidold/PetWatcher-API?color=%23428F7E)](LICENSE)

PetWatcher-API is a REST API built with latest technologies and security concepts in mind and aims to provide a set of
functionalities for managing any desired amount of pets and homes the pets live in.

At first, this might sound like a weird thing for an API and subsequent end-user applications (such as apps, etc.) but
rest assured, once you have more than two cats, for example, or are responsible for taking care of your friends' pets,
PetWatcher-API comes in handy. Ever wondered how *Crazy Cat Lady* keeps an eye on so many cats? Now you know!

## Installation & Usage
PetWatcher-API is currently under heavy development, and it is **not recommended** to install or even run the API until its
very first release. In addition, a database schema is required which is currently also not yet publicly available.

There is currently no publicly available API documentation but PetWatcher-API aims to provide a detailed and concise
documentation adhering to the [OpenAPI specification](https://openapis.org) for the first as well as upcoming releases.

If you would like to test the API before the first release or give feedback, please reach out to contact@petwatcher.app.

## Configuration
There are several configuration options that allow customizing PetWatcher-API to personal needs. To get a basic overview
of which options are available, have a closer look at the [.env.example](.env.example) file. In a real world scenario,
you would remove *.example* from the file name to configure PetWatcher-API. There's currently no option to opt for
another filename. If not stated otherwise, all fields located in the environment file are **mandatory**.

### Database
To configure a database connection, which is required to run the API at all times, the following and most likely self-explaining
configuration parameters are available:
```
# Database
DB_DRIVER="mysql"
DB_HOST="127.0.0.1"
DB_NAME="dbname"
DB_USER="user"
DB_PASSWORD="password"
DB_CHARSET="utf8"
DB_COLLATION="utf8_unicode_ci"
DB_PREFIX=""
```

### Image Upload
PetWatcher-API supports uploading images for pets and homes, therefore you can choose where exactly these images should
be uploaded and how big they are allowed to be. The `MAX_SIZE` parameter supports several values such as `B`, `KB`, `MB`
and `GB`. For a complete overview of file sizes that can be configured, please have a look at the
[Respect\Validation documentation](https://respect-validation.readthedocs.io/en/2.0/rules/Size/).
```
# Image upload
UPLOAD_DIR="/a/very/nice/path/"
MAX_SIZE="1MB"
```

### Logging
The `LOG_DIR` parameter defines the directory in which a text file - holding important information regarding any actions
performed by the API, warnings and errors that occur - is stored. Depending on the `DEBUG` parameter, the amount of logged
information varies.
```
# Logging
LOG_DIR="/another/very/nice/path/"
```

### Security
To secure and verify any calls that are made to the API, [JSON Web Tokens](https://jwt.io) are used. The `ACCESS_TOKEN_SECRET`
parameter stores the secret used to hash the token on the server side before it is sent to the client. This is later on
important to verify that a client has not manipulated the data stored in the token. The `REFRESH_TOKEN_SECRET` parameter
stores the secret used for generating refresh tokens for easier authentication.

It is very important that these two secrets are **private**, **not reused** and **follow common password practices**. In
the case of a compromised secret, an attacker is able to perform **any** actions **without verification**.
```
# Security
ACCESS_TOKEN_SECRET="averysecretsecret"
REFRESH_TOKEN_SECRET="anotherverysecretsecret"
```

### Miscellaneous
The `PRODUCTION` parameter defines whether the API caches certain parts of the code to increase performance. It is only
recommended to set `PRODUCTION` to `false` if you are actively changing the code in a development environment.

The `DEBUG` parameter defines whether detailed error messages (including file locations and database error codes) are
displayed if something goes wrong. It is highly recommended to only set the value to `true` for maintenance purposes or
in a development environment.
```
# Miscellaneous
PRODUCTION=true
DEBUG=false
```

## Contributing
Please refer to [CONTRIBUTING.md](CONTRIBUTING.md) for information on how to contribute to PetWatcher-API.

## Security
If you want to find out which version(s) are currently supported or discover any security related issues, please **do not**
use the issue tracker and instead refer to [SECURITY.md](SECURITY.md) for detailed information.

## License
PetWatcher-API is licensed under the MIT license. See [LICENSE](LICENSE) for more information.
