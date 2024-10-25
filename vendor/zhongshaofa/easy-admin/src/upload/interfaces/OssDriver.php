<?php

namespace EasyAdmin\upload\interfaces;

interface OssDriver
{

    public function save($objectName,$filePath);

}