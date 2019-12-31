# Types

- [Boolean](#boolean)
- [Number](#number)
- [String](#string)
- [Function](#function)
- [Array](#array)
- [Null](#null)
- [Any Type](#any-type)

## Boolean

- Names: `boolean`, `bool`

A boolean can either be [true](constants/true) or [false](constants/false).

## Number

- Names: `number`, `num`, `integer`, `int`

## String

- Names: `string`, `str`

## Function

- Names: `function`, `func`, `routine`

## Array

- Names: `array`, `arr`

Instantiated using an [array](statements/array) or [range](statements/range) statement. 

## Null

- Names: `null`, `none`, `void`, `nil`

Null only has one possible value: [null](constants/null). It is used to indicate nothingness.

## Any Type

- Names: `any_type`, `anytype`, `mixed`

Every variable is implicitly "any type" if it wasn't bound to be a specific type. Nevertheless, you might still want to declare this explicitly for the sake of code readability:

    local any_type var = 1337;
    set var = "Hello, world!"; # Not an error because the "Any Type" contract can't be broken.
    print var; # Hello, world!

[[ Run it online ]](https://utopia.sh/?code=local+any_type+var+%3D+1337%3B%0D%set+var+%3D+%22Hello%2C+world%21%22%3B+%23+Not+an+error+because+the+%22Any+Type%22+contract+can%27t+be+broken.%0D%0Aprint+var%3B+%23+Hello%2C+world%21)
