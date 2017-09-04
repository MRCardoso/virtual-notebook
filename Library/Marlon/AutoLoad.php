<?php
	class AutoLoad
	{
		protected $ext;
		protected $prefix;
		protected $sufix;
	
	   /**
	   | ----------------------------------------------------------------------------
	   | Sets the local path to the root of the script
	   | ----------------------------------------------------------------------------
	   * @param string $path full path to the script
	   */
	   public function setPath($path)
	   {
		   set_include_path($path);
	   }
	
	   /**
	   | ----------------------------------------------------------------------------
	   | Sets the extension of the file to be exported
	   | ----------------------------------------------------------------------------
	   * @param string $ext the extension without the dot
	   */
	   public function setExt($ext)
	   {
		   $this->ext = ".{$ext}";
	   }
	
	   /**
	   | ----------------------------------------------------------------------------
	   | Sets whether there is anything to put before the file name
	   | ----------------------------------------------------------------------------
	   * @param string $prefix what goes before the file name
	   */
	   public function setPrefix($prefix)
	   {
		   $this->prefix = $prefix;
	   }
	
	   /**
	   | ----------------------------------------------------------------------------
	   | Sets whether there is anything to put after the file name
	   | ----------------------------------------------------------------------------
	   * @param string $sufix what goes after the file name
	   */
	   public function setSufix($sufix)
	   {
		   $this->sufix = $sufix;
	   }

		/**
		| ----------------------------------------------------------------------------
		| Turns the class on path to the corresponding file
		| ----------------------------------------------------------------------------
		* @param string $className full path to the script
		*
		* @return  $fileName the path to the file of the class
		*/
		protected function setFilename($className)
		{
			$className = ltrim($className, "\\");
			$fileName  = '';
			$namespace = '';
			
			if ($lastNsPos = strrpos($className, "\\"))
			{
				$namespace = substr($className, 0, $lastNsPos);
				$className = substr($className, ($lastNsPos + 1));
				$className = "{$this->prefix}{$className}{$this->sufix}";
				$fileName  = str_replace("\\", DS, $namespace) . DS;
			}
			
			$fileName .= "{$className}{$this->ext}";
			
			return $fileName;
		}

		/**
		| ----------------------------------------------------------------------------
		| Load files of the library
		| ----------------------------------------------------------------------------
		* @param string $className the class to be loaded
		*/
		public function loadCore($className)
		{
			$fileName = $this->setFilename($className);
			$fileName = get_include_path() . DS . $fileName;

			if (is_readable($fileName))
			{
				require $fileName;
			}
		}
	}
