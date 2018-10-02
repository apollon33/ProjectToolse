<?php
namespace common\library\config;

use common\library\exceptions\{
    DataNotFoundException,
    FileNotExistException,
    FileWriteException,
    ParseIniFileException,
    RequiredParamException
};

class IniManager implements ConfigInterface
{
    /**
     * Actual data from config file
     * @var null|array
     */
    private $data;

    /**
     * Config file location
     * @var string
     */
    private $filePath;

    /**
     * Config source options
     * @var int
     */
    private $fileMode;

    /**
     * Config section to read
     * @var string
     */
    protected $section;

    /**
     * @inheritdoc
     * @throws RequiredParamException
     */
    public function __construct(string $filePath, string $section, int $mode = ConfigInterface::STRICT)
    {
        if (empty($filePath)) {
            throw new RequiredParamException('Config file name not specified.');
        }

        $this->filePath = $filePath;
        $this->section = $section;
        $this->fileMode = $mode;
    }

    /**
     * Actual read source function
     * @throws FileNotExistException
     * @throws ParseIniFileException
     */
    public function load()
    {
        if ($this->data !== null && ($this->fileMode & ConfigInterface::ALLOW_RELOAD) !== ConfigInterface::ALLOW_RELOAD) {
            return;
        }
        $fileInfo = new \SplFileInfo($this->filePath);

        if ($fileInfo->isLink()) {
            throw new FileNotExistException("Config file cannot be a link");
        }
        if ($fileInfo->isDir()) {
            throw new FileNotExistException("Config file cannot be a directory");
        }

        if ($fileInfo->isFile() === false) {
            $canCreate = ($this->fileMode & ConfigInterface::ALLOW_CREATE) === ConfigInterface::ALLOW_CREATE;
            if (!$canCreate) {
                throw new FileNotExistException('The file does not exist.');
            }
            if ($fileInfo->getPathInfo()->isDir() === false && mkdir($fileInfo->getPath(), 0775, true) === false) {
                throw new FileNotExistException('Could not create file');
            }
        $this->writeFile($fileInfo, "[{$this->section}]" . PHP_EOL, $canCreate);
        }

        $data = parse_ini_file($this->filePath, true);
        if ($data === false || empty($data[$this->section]) || !is_array($data[$this->section])) {
            if (($this->fileMode & ConfigInterface::ALLOW_EMPTY) !== ConfigInterface::ALLOW_EMPTY) {
                throw new ParseIniFileException('Can not read file.');
            }
            $data = [
                $this->section => []
            ];
        }

        $this->data = $data;
    }

    /**
     * Actual file writer
     * @param \SplFileInfo $fileInfo File object
     * @param string $data data to be written
     * @param bool $allowEmpty if allowed then will not check for file to be writtable, such as uncreated file
     * @throws FileWriteException
     * @throws \RuntimeException
     */
    protected function writeFile(\SplFileInfo $fileInfo, $data, bool $allowEmpty = false)
    {
        if (!($allowEmpty || $fileInfo->isWritable())) {
            throw new FileWriteException("File not writable");
        }

        $file = $fileInfo->openFile("w");
        $bytesWritten = $file->fwrite($data);
        $file = null;

        if ($bytesWritten === 0) {
            throw new FileWriteException("Couldn't write to file");
        }
    }

    /**
     * @inheritDoc
     * @throws FileNotExistException
     * @throws ParseIniFileException
     * @throws FileWriteException
     */
    public function get(string $section, string $key)
    {
        $data = $this->getData();

        if (!array_key_exists($section, $data) || !is_array($data[$section]) || !array_key_exists($key, $data[$section])) {
            throw new DataNotFoundException();
        }

        return $data[$section][$key];
    }

    /**
     * @inheritDoc
     * @throws DataNotFoundException
     * @throws FileNotExistException
     * @throws ParseIniFileException
     */
    public function set(string $section, string $key, $value)
    {
        $data = $this->getData();
        $data[$section][$key] = $value;
        $this->setData($data);

        $this->writeFile(new \SplFileInfo($this->filePath), $this->arrToIni($data));

        return true;
    }

    /**
     * @return array
     * @throws FileNotExistException
     * @throws ParseIniFileException
     */
    protected function getData()
    {
        if ($this->data === null) {
            $this->load();
        }

        return $this->data;
    }

    /**
     * Internal data set to preserve module statuses
     * @param array $data
     */
    protected function setData(array $data)
    {
        $this->data = $data;
    }

    /**
     * @param array $array
     * @param array $parent
     * @return string
     */
    protected function arrToIni(array $array, array $parent = [])
    {
        $out = '';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $section = array_merge((array)$parent, (array)$key);
                $out .= '[' . join('.', $section) . ']' . PHP_EOL;
                $out .= $this->arrToIni($value, $section);
            } else {
                $out .= $key . ' = ' . $value . PHP_EOL;
            }
        }
        return $out;
    }

}
