<?php

namespace App\Controller;

class AbstractController
{
    protected static function renderView(string $path, ?array $parameters = [])
    {
        if (!file_exists(__BASE_DIR__ . "/templates/${path}.php")) {
            return new \Exception("Aucun template trouvé avec le nom \"${path}\".");
        }

        if (count($parameters) > 0) {
            foreach ($parameters as $key => $value) {
                $vars[$key] = $value;
            }
        }

        return include __BASE_DIR__ . '/templates/' . $path . '.php';
    }
}
