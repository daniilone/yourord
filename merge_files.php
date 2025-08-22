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
        'app/Http/Controllers', // Папка с контроллерами
//        'app/Http/Controllers/Auth',
        'database/migrations',  // Папка с миграциями в Laravel/Models',
        'resources/views',      // Папка с Blade-шаблонами
        'routes',              // Папка с маршрутами
        'app/Models',          // Папка с моделями
    ],
    'extensions' => ['php', 'blade.php'], // Разрешённые расширения файлов
    'exclude_dirs' => ['vendor', 'node_modules', 'storage'], // Папки, которые нужно исключить
    'output_file' => 'merged_files.txt', // Имя выходного файла
    'base_path' => __DIR__, // Базовый путь (корень проекта)
];

// Функция для рекурсивного обхода директорий
function mergeFiles($config) {
    $outputContent = '';
    $fileCount = 0;
    $errors = [];

    // Открываем выходной файл для записи
    $outputHandle = fopen($config['output_file'], 'w');
    if (!$outputHandle) {
        echo "Ошибка: Не удалось создать выходной файл {$config['output_file']}\n";
        return;
    }

    foreach ($config['directories'] as $dir) {
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
    }

    // Записываем содержимое в выходной файл
    fwrite($outputHandle, $outputContent);
    fclose($outputHandle);

    // Выводим статистику
    echo "Обработано файлов: {$fileCount}\n";
    echo "Результат сохранён в: {$config['output_file']}\n";
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
