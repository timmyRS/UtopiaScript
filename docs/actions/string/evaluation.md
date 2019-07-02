# Evaluation

- Syntax: `<string>`

Evaluates the string as UtopiaScript code within a separate block:

    local greeting "Hi";
    {
        local greeting "Hello";
        print_line greeting;
    };
    print_line greeting;

[[ Run it online ]](https://utopia.sh/?code=local+greeting+%22Hi%22%3B%0D%0A%7B%0D%0A++++local+greeting+%22Hello%22%3B%0D%0A++++print_line+greeting%3B%0D%0A%7D%3B%0D%0Aprint_line+greeting%3B)
