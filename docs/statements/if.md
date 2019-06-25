# if

- Alias: `?`
- Syntax: `if <condition> <code> [otherwise <code>]`

The if statement changes the code's branch, depending on a given condition.

For example, we could ask the user if he wants conditionals, using [read](read):

    print "Do you want conditionals? [Y/n] ";
    local choice read;
    # The user could've answered anything so we're simplifying "choice" to a boolean:
    set choice = choice tolowercase not_equal_to "n";
    if choice = yes {
        print "Great!";
    } otherwise {
        print ":(";
    }; 

Note that `otherwise` here isn't truly a statement, but rather an argument, which is why the semicolon (`;`) is only placed after it. Nevertheless, you can use `else` and `|` as shorthands for it.
