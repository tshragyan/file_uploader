<?php


namespace App\Services;

class FileService
{
    const TEMP_FILE_PATH = 'app/temp';
    const FILE_FINAL_PATH = 'app/public/uploads/';

    /**
     * @param array $data
     * @return bool
     */
    public function uploadFile(array $data): array
    {
        try {
            $fileName = $data['fileName'];
            $index = $data['index'];
            $totalCount = $data['totalCount'];
            $file = $data['file'];
            $tempPath = storage_path(self::TEMP_FILE_PATH);
            $file->move($tempPath, $fileName . '.part' . $index);

            if ($index + 1 == $totalCount) {
                return  $this->mergeFiles($fileName, $totalCount);
            }
            return ['status' => true];

        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * @param string $fileName
     * @param int $count
     * @return bool
     */
    private function mergeFiles(string $fileName, int $count): array
    {
        try {
            $tempPath = storage_path(self::TEMP_FILE_PATH);
            $dirPath = storage_path(self::FILE_FINAL_PATH);
            $finalPath = storage_path(self::FILE_FINAL_PATH . $fileName);

            if (!file_exists($dirPath)) {
                mkdir($dirPath, 0777, true);
            }

            if (!file_exists($finalPath)) {
                file_put_contents($finalPath, "");
            }

            $finalFile = fopen($finalPath, 'wb');

            for ($i = 0; $i < $count; $i++) {
                $chunkPath = $tempPath . '/' . $fileName . '.part' . $i;
                $chunkFile = fopen($chunkPath, 'rb');
                stream_copy_to_stream($chunkFile, $finalFile);
                fclose($chunkFile);
                unlink($chunkPath);
            }

            fclose($finalFile);

            return ['status' => true];
        } catch (\Throwable $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }

    }
}
