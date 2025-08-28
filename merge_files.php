<?php

/**
 * Скрипт для объединения содержимого файлов из указанных папок в один файл.
 *
 * Использование:
 * php merge_files.php
 *
 * Настройки задаются в массиве $config.
 */

// Конфигурация
$config = [
    'directories' => [
        'controllers' => 'app/Http/Controllers', // Папка с контроллерами
        'models' => 'app/Models',          // Папка с моделями
//        'app/Http/Controllers/Auth',
        'migrations'=> 'database/migrations',  // Папка с миграциями в Laravel/Models',
        'views' => 'resources/views',      // Папка с Blade-шаблонами
        'routes' => 'routes',              // Папка с маршрутами
        'bootstrap' => 'bootstrap',
        'config' => 'config'
    ],
    'extensions' => ['php', 'blade.php'], // Разрешённые расширения файлов
    'exclude_dirs' => ['vendor', 'node_modules', 'storage'], // Папки, которые нужно исключить
    'output_file' => 'merged_files.txt', // Имя выходного файла
    'base_path' => __DIR__, // Базовый путь (корень проекта)
];

// Функция для рекурсивного обхода директорий
function mergeFiles($config) {

    $fileCount = 0;
    $errors = [];


    foreach ($config['directories'] as $fileName => $dir) {
        $outputContent = '';
        $path = "merged_files/{$fileName}.txt";
        // Открываем выходной файл для записи
        $outputHandle = fopen($path, 'w');
        if (!$outputHandle) {
            echo "Ошибка: Не удалось создать выходной файл {$path}\n";
            return;
        }

        $fullDir = $config['base_path'] . DIRECTORY_SEPARATOR . $dir;
        if (!is_dir($fullDir)) {
            $errors[] = "Папка {$dir} не существует";
            continue;
        }

        // Рекурсивный обход папки
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($fullDir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $file) {
            // Пропускаем директории
            if ($file->isDir()) {
                // Проверяем, не входит ли папка в исключения
                $relativePath = str_replace($config['base_path'] . DIRECTORY_SEPARATOR, '', $file->getPathname());
                if (isExcludedDir($relativePath, $config['exclude_dirs'])) {
                    continue;
                }
                continue;
            }

            // Проверяем расширение файла
            $extension = $file->getExtension();
            if (!in_array($extension, $config['extensions'])) {
                continue;
            }

            // Читаем содержимое файла
            $content = file_get_contents($file->getPathname());
            if ($content === false) {
                $errors[] = "Не удалось прочитать файл: {$file->getPathname()}";
                continue;
            }

            // Получаем относительный путь файла
            $relativePath = str_replace($config['base_path'] . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $relativePath = str_replace("\\", '/', $relativePath);

            // Добавляем разделитель и содержимое файла
            $outputContent .= "\n\n";
            $outputContent .= "========== File: {$relativePath} ==========\n";
            $outputContent .= $content;

            $fileCount++;
        }

        // Записываем содержимое в выходной файл
        fwrite($outputHandle, $outputContent);
        fclose($outputHandle);
    }


    // Выводим статистику
    echo "Обработано файлов: {$fileCount}\n";
    echo "Результат сохранён в: /merged_files/\n";
    if (!empty($errors)) {
        echo "Ошибки:\n";
        foreach ($errors as $error) {
            echo "- {$error}\n";
        }
    }
}

// Проверяет, находится ли путь в исключённых директориях
function isExcludedDir($path, $excludeDirs) {
    foreach ($excludeDirs as $excludeDir) {
        if (strpos($path, $excludeDir) === 0 || strpos($path, DIRECTORY_SEPARATOR . $excludeDir . DIRECTORY_SEPARATOR) !== false) {
            return true;
        }
    }
    return false;
}

// Запускаем скрипт
mergeFiles($config);

?>
