<?php

namespace EnvParser;

/**
 * Env File Parser Class
 */
class Parser
{
    /**
     * Parse .env file and return its content as array of key/values.
     * 
     * @param string $envFilePath
     * @return array
     */
    public function parse($envFilePath)
    {
        if (!file_exists($envFilePath)) {
            throw new \InvalidArgumentException(
                "The file {$envFilePath} doesn't exists"
            );
        }
        
        $file = fopen($envFilePath, 'r');

        $fileContent = [];

        while (!feof($file)) {
            $line = fgets($file);
            $line = str_replace(["\n", "\r"], '', $line);

            if (!empty($line)) {
                // if the line starts with # then it's comment
                if ($line[0] == '#') {
                    continue;
                }

                // extract key and value
                $segments = explode('=', $line, 2);
                $key = trim($segments[0]);
                $value = $segments[1];

                // remove comments from line
                if (((strlen($value) - 1) > 0) &&
                    ($value[strlen($value) - 1] != '"') && 
                    ($value[strlen($value) - 1] != '\'')
                ) {
                    $value = explode('#', $value)[0];
                }

                // check for numeric string
                $isNumericString = false;
                if (isset($value[0]) &&
                    (trim($value, " ")[0] == '\'' 
                    || trim($value, " ")[0] == '"') && 
                    is_numeric(trim($value, " \"'"))
                ) {
                    $isNumericString = true;
                }

                // remove spaces and quotes
                $value = trim($value, " \"'");
                
                // handle variables ${foo}
                $matches = [];
                
                preg_match_all(
                    '~^\$\{[^${}]*|^[^${}]*\}$|\$\{[^${}]*\}~',
                    $value,
                    $matches
                );

                if (!empty($matches[0])) {
                    foreach ($matches[0] as $var) {
                        $var = str_replace(["$", "{", "}"], '', $var);
                        
                        // get default value
                        $varValues = explode(':=', $var);

                        $value = str_replace(
                            '${' . $var . '}',
                            $fileContent[$varValues[0]] ?? $varValues[1] ?? '',
                            $value
                        );
                    }
                }

                // handle data types
                if (strtolower($value) == 'null') {
                    $value = null;
                }
                else if (strtolower($value) == 'true') {
                    $value = true;
                }
                else if (strtolower($value) == 'false') {
                    $value = false;
                }
                else if (is_numeric($value) && !$isNumericString) {
                    if (strpos($value, '.') !== false) {
                        $value = (float) $value;
                    } else {
                        $value = (int) $value;
                    }
                }

                // save result
                $fileContent[$key] = $value;
                $_ENV[$key] = $value;
                $_SERVER[$key] = $value;
                putenv("{$key}={$value}");
            }
        }
        
        return $fileContent;
    }
}
