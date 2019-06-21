# UtopiaScript's CLI Command

Once you've installed UtopiaScript via [Cone](https://getcone.org/) using `cone get utopiascript`, you should have access to the `utopia` CLI command, which can be used to execute UtopiaScript files by passing them as arguments (e.g., `utopia file.us`). However, it also has some additional features:

- [REPL](#repl)
- [Stopwatch](#stopwatch)
- [Debug Mode](#debug-mode)

## REPL

If you'd like to understand UtopiaScript statement-by-statement, enter a [read-eval-print loop](https://en.wikipedia.org/wiki/Read%E2%80%93eval%E2%80%93print_loop) using `utopia repl`, which will allow you to enter a statement (`>`), execute it by pressing enter, and receive its return value after `=`:

    > 1 + 2
    = 3

Any output will be presented after `<`:

    > print 1 + 2
    < 3
    = 3

And now you've already learned that `print` returns what it prints!

You can also submit incomplete code which will allow you to continue input into the next line, if detected:

    > print "Hello
    >> world"
    < Hello
    world
    = Hello
    world

## Stopwatch

When using the `--stopwatch` or `-t` flag, the time it took to parse and execute the given code will be printed after it has finished execution.

## Debug Mode

When using the `--debug` or `-d` flag, debug mode will be enabled, resulting in verbose output which should hopefully reveal how your code is understood.
Note that the [Debug Extension](extensions.md#debug) is available to toggle debug mode on-demand, which is especially useful for bigger scripts.
