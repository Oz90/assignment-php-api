<?php

class App
{
    private static $errors = [];

    /*Static array of all current genres in the api, which I use to check if
     *Users input query of genre exists.*/

    private static $genres = [
        "rpg", "action", "racing", "simulation", "rts"
    ];

    /*Main function 
    *1. takes data as parameter
    * */
    public static function main($data)
    {


        // Check if genre is set in the query string, else catch and return error.
        if (isset($_GET["genre"])) {
            try {
                $data = self::validate_genre_request($data);
            } catch (Exception $error) {
                array_push(self::$errors, ['Genre' => $error->getMessage()]);
            }
        }

        // Check if limit is set in the query string, else catch and return error.
        if (isset($_GET["limit"])) {
            try {
                $data = self::validate_limit_request($data);
            } catch (Exception $error) {
                array_push(self::$errors, ['Limit' => $error->getMessage()]);
            }
        }

        // If no errors, we render out the data requested by the user.
        if (!self::$errors) {
            self::render_data($data);
        } else self::render_data(self::$errors);
    }

    // Basic function to render all data, public so it can be called from index.php
    public static function render_data($data)
    {
        // Added a "shuffle effect" of the data to make it more dynamic before rendering.
        shuffle($data);

        // Creates a JSON out of my data ($data). 
        $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Echo the JSON
        echo $json;
    }


    /**A method that takes an array as parameter and creates a new "filtered array"
     *which is based on the genre that the user requested in the query string*/
    private static function get_requested_data($array)
    {
        // $requestedData = array_filter($array, function ($game) {
        //     return $game['genre'] === self::get_query('genre');
        // });
        // return $requestedData;
        $requestedData = [];
        foreach ($array as $value) {
            if ($value['genre'] === self::get_query('genre')) {
                array_push($requestedData, $value);
            }
        }
        return $requestedData;
    }

    /**
     * A validation method that checks if the genre exists in the genres of the API. 
     * If not, exception is thrown
     * If genre exists, it calls on the get_request_data method.
     */
    private static function validate_genre_request($data)
    {
        $genre = strtolower(self::get_query("genre"));

        if (!in_array($genre, self::$genres)) {
            throw new Exception('Genre not found');
        }
        return self::get_requested_data($data);
    }

    /**
     * A validation method for the limit set by the user in the query string
     * checks if its set, if its numeric and 1-20 range.
     * If it is, then it returns a new array using the slice method to pick out as many objects
     * as requested by the user in the limit query.
     */
    private static function validate_limit_request($data)
    {
        $limit = self::get_query("limit");
        if (isset($limit) && $limit !== false && (!is_numeric($limit) || $limit < 1 || $limit > 20 || $limit % 1 !== 0)) {
            throw new Exception('Limit must be between 1 and 20.');
        }
        return array_slice($data, 0, $limit);
    }

    /**
     * Method to get the query and sanitize it before returning it.
     */
    private static function get_query($query)
    {
        return isset($_GET[$query]) ? filter_var(strtolower($_GET[$query]), FILTER_SANITIZE_STRING) : false;
    }
};
