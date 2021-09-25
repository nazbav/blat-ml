<?php
/**
 * @param $url
 * @return array|false|string|string[]|void|null
 */
function read_doc($url)
{
//    $document = new doc();
//    $document->read("tempf.txt");


    $url = mb_convert_encoding($url, 'windows-1251', mb_detect_encoding($url));


    $context = stream_context_create(
        [
            'http' => [
                'method' => 'GET',
                'protocol_version' => '1.1',
                'header' => [
                    'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:24.0) Gecko/20100101 Firefox/24.0',
                    'Connection: close',
                ],
            ],
        ]
    );
    $file = "tempf.docx";
    $stream = fopen($url, 'r', false, $context);
    if ($stream) {
        $content = stream_get_contents($stream); //тут получаем страницу
        file_put_contents($file, $content);


        //   unlink("tempf.txt");
        // return $document->parse();

//    /$url = strtr($url, ['http:' => 'https:']);
        //  $doc = file_get_contents($url);
        //  file_put_contents("tempf.docx", $doc);
//    $document = new doc();
//
//    $document->read("tempf.txt");
//
    }

    $converter = new DocxToTextConversion($file);
    $content = $converter->convertToText();

    if (is_file($file)) {
        unlink($file);
    }


    return $content;
}


/**
 *
 */
class DocxToTextConversion
{
    private $document;

    /**
     * @param $DocxFilePath
     */
    public function __construct($DocxFilePath)
    {
        $this->document = $DocxFilePath;
    }

    /**
     * @return array|false|string|string[]|void|null
     */
    public function convertToText()
    {
        if (isset($this->document) && !file_exists($this->document)) {
            return 'File Does Not exists';
        }

        $fileInformation = pathinfo($this->document);
        $extension = $fileInformation['extension'];
        if ($extension == 'doc' || $extension == 'docx') {
            if ($extension == 'doc') {
                return $this->extract_doc();
            } elseif ($extension == 'docx') {
                return $this->extract_docx();
            }
        } else {
            return 'Invalid File Type, please use doc or docx word document file.';
        }
    }

    /**
     * @return array|string|string[]|null
     */
    private function extract_doc()
    {
        $fileHandle = fopen($this->document, 'r');
        $allLines = @fread($fileHandle, filesize($this->document));
        $lines = explode(chr(0x0D), $allLines);
        $document_content = '';
        foreach ($lines as $line) {
            $pos = strpos($line, chr(0x00));
            if (($pos !== false) || (strlen($line) == 0)) {
            } else {
                $document_content .= $line . ' ';
            }
        }
        $document_content = preg_replace("/[^a-zA-Z0-9\s\,\.\-\n\r\t@\/\_\(\)]/", '', $document_content);
        return $document_content;
    }

    /**
     * @return false|string
     */
    private function extract_docx()
    {
        $document_content = '';
        $content = '';

        $zip = @zip_open($this->document);

        if (!$zip || is_numeric($zip)) {
            return false;
        }

        while ($zip_entry = @zip_read($zip)) {
            if (@zip_entry_open($zip, $zip_entry) == false) {
                continue;
            }

            if (@zip_entry_name($zip_entry) != 'word/document.xml') {
                continue;
            }

            $content .= @zip_entry_read($zip_entry, @zip_entry_filesize($zip_entry));

            @zip_entry_close($zip_entry);
        }

        @zip_close($zip);

        $content = str_replace('</w:r></w:p></w:tc><w:tc>', ' ', $content);
        $content = str_replace('</w:r></w:p>', "\r\n", $content);
        $document_content = strip_tags($content);

        return $document_content;
    }
}