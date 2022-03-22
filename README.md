<div align="center">
    <h1> Flysystem Adapter for Cloudinary API</h1>
</div>

[![Latest Version on Packagist](https://img.shields.io/packagist/v/jasirilabs/flysystem-cloudinary.svg?style=flat-square)](https://packagist.org/packages/jasirilabs/flysystem-cloudinary)
[![Total Downloads](https://img.shields.io/packagist/dt/jasirilabs/flysystem-cloudinary.svg?style=flat-square)](https://packagist.org/packages/jasirilabs/flysystem-cloudinary)
![GitHub Actions](https://github.com/jasirilabs/flysystem-cloudinary/actions/workflows/main.yml/badge.svg)

The package contains a [flysystem](https://flysystem.thephpleague.com/) adapter for cloudinary. Under the hood [Cloudinary PHP SDK](https://github.com/cloudinary/cloudinary_php)is used.

## Disclaimer
> _This package is still under active development but fill free to try out leave a PR or file an issue incase of any challenge_



## Installation

You can install the package via composer:

```bash
composer require jasirilabs/flysystem-cloudinary
```


## Usage

Then follow the steps on using [custom filesystem](https://laravel.com/docs/9.x/filesystem#custom-filesystems) with laravel.

**Quick Start**

```env
FILESYSTEM_DISK=
CLOUDINARY_NAME=
CLOUDINARY_KEY=
CLOUDINARY_SECRET=
```

Add cloudinary disk in `filesystem.php`

```php
		... 
	'disk' => 
	[
		...

		   'cloudinary' => [
            'driver' => 'cloudinary',
            'name' => env('CLOUDINARY_NAME'),
            'key' => env('CLOUDINARY_KEY'),
            'secret' => env('CLOUDINARY_SECRET'),
	]

```

Add the following on boot method of `AppServiceProvider.php`  file

```php
Storage::extend('cloudinary', function ($app, $config) {

            $configuration = new Configuration();
            $configuration->cloud->cloudName = $config['name'];
            $configuration->cloud->apiKey = $config['key'];
            $configuration->cloud->apiSecret = $config['secret'];
            $configuration->url->secure = true;

            $cloudinary = new Cloudinary($configuration);
            $adapter = new CloudinaryAdapter($cloudinary);

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config
            );
        });
```




### Testing

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email hello@brunoalfred.me instead of using the issue tracker.

## Credits

-   [Bruno Alfred](https://github.com/jasirilabs)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## PHP Package Boilerplate

This package was generated using the [PHP Package Boilerplate](https://laravelpackageboilerplate.com) by [Beyond Code](http://beyondco.de/).
