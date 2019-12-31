# range

- Syntax: `range [from] <start> [to|-] <end>`

Creates an array containing a range of numbers:

```
print_line range from 1 to 10; # array 1 2 3 4 5 6 7 8 9 10
```

[[ Run it online ]](https://utopia.sh/?code=print_line+range+from+1+to+10%3B%0D%0A)

or a range of characters:

```
print_line range "A" "Z"; # array "A" "B" "C" "D" ... "Z"
```

[[ Run it online ]](https://utopia.sh/?code=print_line+range+%22A%22+%22Z%22%3B%0D%0A)

The [`array` statement](array) also supports ranges:

```
print_line array 1 - 10; # array 1 2 3 4 5 6 7 8 9 10
```

[[ Run it online ]](https://utopia.sh/?code=print_line+array+1+-+10%3B%0D%0A)
