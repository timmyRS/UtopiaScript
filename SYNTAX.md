This document contains any information that is yet to be put into the [documentation](https://docs.utopia.sh/).

## Strings

You can use `"`, `'`, and <code>`</code> to encapsulate your strings:

    print "A" 'B' `C`; # ABC

You can also use `{ this style }` for code:

    print {You can also use this style for code:
    print {
    ...
    };};
    # You can also use this style for code:
    # print {
    # ...
    # };

Note that any `{` within strings of this type will have to be followed by a matching `}`. 

Of course, you might not want to add new line characters to your strings using an actual new line:

    print "Hello
    ";

Instead, you can use the `CR`, `LF`, `NL`, `CRLF`, and `CRNL` constants:

    print "Hello"CRLF;

Where `LF` (line feed) and `NL` (new line) are ASCII character 10, and `CR` (carriage return) is ASCII character 13.

Alternatively, you can use `print_line "Hello";` to the same effect.

Also note the presence of the `EOL` constant, containing the line ending appropriate for the operating system.

There are also some actions to manipulate strings:

    print "HELLO WORLD" tolowercase; # hello world
    print ("hello" touppercase) ("WORLD" tolowercase); # HELLOworld

## Numbers

If you're a being with an intelligence matching the average human, this won't need any explanation:

    print 2 + 2; # 4
    print 4 - 1; # 3
    print 0.5 * 2; # 1
    print 3 / 4; # 0.75

However, if you're like the people I've surveyed for this section, the following operations might be new to you. As such, I've linked their Wikipedia articles. 

- `print 2 ^ 8; # 256` ([Exponentiation](https://en.wikipedia.org/wiki/Exponentiation))
- `print 3 % 4; # 3` ([Modulo Operation](https://en.wikipedia.org/wiki/Modulo_operation))
- `print 3!; #6` ([Factorial](https://en.wikipedia.org/wiki/Factorial))

Note that UtopiaScript uses a literal order of operations:

    print 2 + 2 * 2; # 8
    print 2 + (2 * 2); # 6

There are also some actions to manipulate numbers:

    print m_e floor; # 2
    print m_pi ceil; # 4
    print 3.5 round; # 3.5

Obviously, doing everything in only one way is boring:

    print floor m_e; # 2
    print ceil(m_pi); # 4

## Functions

Functions are strings which happen to represent valid UtopiaScript code:

    local myFunc {
        return "myFunc has spoken!";
    };
    print (myFunc); # myFunc has spoken!

Note that `myFunc` is wrapped in `( parentheses )` because we want to echo the return value of the function and not value of the string.

Functions can also accept parameters:

    global myAdd number:a,number:b {
         return a + b;
    };
    print myAdd 1 2; # 3

In this case, our function has two parameters: The `number`s `a` and `b`; and the function simply returns the result of `a + b`.

You can also use `void` to declare that our value is a function that doesn't accept parameters so we don't have to use `( parentheses )`:

    local myFunc void {
        return "myFunc has spoken!";
    };
    print myFunc; # myFunc has spoken!

### Return and Exit

Note that the `return` statement will stop execution of the function. Similarly, you can use `exit` anywhere to stop execution of the script:

    local myFunc void {
        exit "Goodnight!";
    };
    myFunc; # Goodnight!
    print "Hi!"; # not executed

You can also use `return` outside of a block to the same effect:

    return "Goodnight!";
    print "Hi!";

## Code Golfing

For code golfing, there are a couple of things to note:

- [Noteworthy aliases](https://docs.utopia.sh/golfing#noteworthy-aliases)
- Spaces are not the only delimiter for literals. For example, tokens such as quotation marks, `@`, etc. also delimit literals, allowing you to use `<"Hi "name` and `myarray@i{<i}`
- Semicolons are not required at the end of a block and when a statement doesn't accept any (further) parameters.

And with all of that in mind, we can convert:

    final myFunc {
        return "Hello, world!";
    };
    print (myFunc);

Into:

    !myFunc{="Hello,world!"};<(myFunc)

Have fun and code golf responsibly! :)
