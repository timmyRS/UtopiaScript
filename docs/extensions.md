# Extensions

- [Built-ins](#built-ins)
    - [Debug](#debug)
    - [PHP Statement](#php-statement)
- [Loading extensions](#loading-extensions)
- [Checking if a statement is available](#checking-if-a-statement-is-available)
- [Writing your own extension](#writing-your-own-extension)

## Built-ins

Some extensions are bundled with UtopiaScript because they're neat to have, but not neat enough to have them enabled in every environment.

### Debug

The debug extension provides `debug <on|off>;` to toggle debug mode on-demand, which is especially useful for debugging bigger scripts. It is enabled by default when using the `utopia` CLI command.

An example using `utopia repl`:

    > debug on;
    <
    </block>
    =
    > print "Hello, world!";
    < <block>print "<string>Hello, world!</string>";<output>Hello, world!</output>
    </block>
    = Hello, world!
    > debug off;
    < <block>debug off;
    =

### PHP Statement

Because UtopiaScript is interpreted in PHP, this extension is no magic. However, using it is not recommended unless you have some ***very*** specific needs.

    php { echo "Hello ".'from '; ?>PHP<?="!";}; # Hello from PHP!

When using the `utopia` CLI command, it can be enabled using the `--enable-php-statement` or `-p` flag.

## Loading extensions

Once you've `composer require`d the extension, to load it into your Utopia, simply call

```PHP
$utopia->loadExtension($extension);
```

where `$extension` is an instance of a class extending `UtopiaScript\Extension` or a string containing the fully-qualified name of one such class.

## Checking if a statement is available

You can simply see if `get_type` returns `statement`:

    unless get_type php = "statement" {
        die "This script requires the PHP statement.";
     };

## Writing your own extension

Your project simply has to expose a class extending `UtopiaScript\Extension` which currently only allows you to provide custom statements.

To write a custom statement, I can only recommend looking at the source code of the built-in [extensions](https://github.com/timmyrs/UtopiaScript/extensions) and [statements](https://github.com/timmyrs/UtopiaScript/src/Statement).
