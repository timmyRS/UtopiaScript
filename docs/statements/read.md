# read

Aliases: `input`, `in`, `>`

Asks the user for a [string](../constructs#string).

You can use `read` like [set](set):

    print "What's your name? ";
    local name;
    read name;
    print "Hello there, " name;

And/or use its return value: 

    print "What's your name? ";
    local name read;
    print "Hello there, " name;
