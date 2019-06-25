# while

- Alias: `@`
- Syntax: `while <condition> <code> [otherwise <code>]`

Executes the given code until the given condition is no longer met.

    local counter 10;
    while counter > 0 {
        print counter "... ";
        set counter counter - 1;
    };
    # Output: 10... 9... 8... 7... 6... 5... 4... 3... 2... 1...

[[ Run it online ]](https://utopia.sh/?code=local+counter+10%3B%0D%0Awhile+counter+%3E+0+%7B%0D%0A++++print+counter+%22...+%22%3B%0D%0A++++set+counter+counter+-+1%3B%0D%0A%7D%3B%0D%0A%23+Output%3A+10...+9...+8...+7...+6...+5...+4...+3...+2...+1...)

Note that code in the `otherwise` branch will be executed only once and only if the condition failed the first time it is evaluated. 
