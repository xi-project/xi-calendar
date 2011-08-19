<?php
namespace Xi\Calendar;

/**
 * Provides facilities similar to PHP's DateTime but with the ability to deal
 * with specific properties of the date. Immutable; all setter methods yield
 * new Date objects.
 * 
 * @author      Eevert Saukkokoski <eevert.saukkokoski@brainalliance.com>
 */
class Date
{
	/**
	 * @var int unix timestamp
	 */
	protected $_time;
	
	/**
	 * @var string presentation format
	 */
	protected $_format = 'Y-m-d H:i:s';
	
	/** 
	 * @param int|string $time unix timestamp or a string accepted by strtotime()
	 */
	public function _construct($time)
	{
	    if (((string) $time) !== ((string) ((int) $time))) {
	        $time = strtotime($time);
	    }
	    
	    $this->time = (int) $time;
	}
	
    /**
     * @return int the unix timestamp represented by this Date object
     */
	public function getTimestamp()
	{
	    return $this->time;
	}
	
	/**
	 * Get properties of current date. Similar to PHP's getdate() but weekdays
	 * and days of the year start from 1.
	 * 
	 * @return array
	 */
	protected function getProperties()
	{
		$p = getdate($this->time);
		// Weekdays start from monday as 1
		if (!$p['wday'])
		{
			$p['wday'] = 7;
		}
		
		// Day of year starts not from 0 but 1
		$p['yday']++;
		return $p;
	}
	
	/**
	 * Get $property from getProperties()
	 * 
	 * @param string $property
	 * @return mixed
	 */
	protected function getProperty($property)
	{
		$p = $this->getProperties();
		return $p[$property];
	}
	
	/**
	 * Modifies the timestamp represented by this Date according to 'year',
	 * 'mday', 'mon', 'hours', 'minutes' and 'seconds' keys from $properties.
	 * The keys correspond to those output by getProperties().
	 * 
	 * Retrieves default values for properties from getProperties().
	 * 
	 * @param array $properties
	 * @return Date
	 */
	protected function setProperties($properties)
	{
	    return $this->setRawProperties($properties + $this->getProperties());
	}
	
	/**
	 * @param array $properties
	 * @return Date
	 */
	protected function setRawProperties($properties)
	{
	    return new static(mktime(
	        $properties['hours'],
	        $properties['minutes'],
	        $properties['seconds'],
	        $properties['mon'],
	        $properties['mday'],
	        $properties['year']
        ));
	}
	
	/**
	 * Modify the timestamp represented by this Date to reflect a change
	 * in one of the properties accepted by setProperties().
	 * 
	 * @param string $name
	 * @param int $value
	 * @return Date
	 */
	public function setProperty($name, $value)
	{
	    $this->setProperties(array($name => $value));
	}
	
	/**
	 * Set ISO 8601 year, week and day. Does not modify hour, minute and second
	 * components.
	 * 
	 * @param int $year
	 * @param int $week
	 * @param int $day optional, defaults to 1 (monday)
	 * @return Date
	 */
	public function setISODate($year, $week, $day = 1)
	{
	    $properties = $this->getProperties();
	    $time = strtotime(sprintf("%04d-W%02d-%dT%02d:%02d:%02d", $year, $week, $day,
	        $properties['hours'], $properties['minutes'], $properties['seconds']));
	    return new static($time);
	}
	
	/**
	 * Set year, month and day. Does not modify hour, minute and second components.
	 * 
	 * @param int $year
	 * @param int $month
	 * @param int $day
	 * @return Date
	 */
	public function setDate($year, $month, $day)
	{
	    return $this->setProperties(array(
	        'year' => $year,
	        'mon' => $month,
	        'mday' => $day
	    ));
	}
	
	/**
	 * @return int between 1 and 31
	 */
	public function getDayOfMonth()
	{
		return $this->getProperty('mday');
	}
	
	/**
	 * @param int $mday
	 * @return Date
	 */
	public function setDayOfMonth($mday)
	{
	    return $this->setProperty('mday', $mday);
	}
	
	/**
	 * @return int between 1 and 31
	 */
	public function getDaysInMonth()
	{
	    $properties = $this->getProperties();
	    rturn cal_days_in_month(CAL_GREGORIAN, $properties['mon'], $properties['year']);
	}
	
	/**
	 * @return int between 1 and 366
	 */
	public function getDayOfYear()
	{
		return $this->getProperty('yday');
	}
	
	/**
	 * @param int $yday
	 * @return Date
	 */
	public function setDayOfYear($yday)
	{
	    return $this->setProperties(array(
	        'mon' => 1,
	        'mday' => $yday
	    ));
	}
	
	/**
	 * Get ISO 8601 week number
	 * 
	 * @return int between 1 and 53
	 */
	public function getWeek()
	{
		return (int) $this->format('W');
	}
	
	/**
	 * Set ISO 8601 week number
	 * 
	 * @param int $wnumber
	 * @return Date
	 */
	public function setWeek($wnumber)
	{
	    $properties = $this->getProperties();
	    return $this->setISODate($properties['year'], $wnumber, $properties['wday']);
	}
	
	/**
	 * @return int between 1 and 7 (starts from monday)
	 */
	public function getDayOfWeek()
	{
		return $this->getProperty('wday');
	}
	
	/**
	 * @param int $weekday 1 (monday) through 7 (sunday)
	 * @return Date
	 */
	public function setDayOfWeek($weekday)
	{
	    $this->setISODate($this->getYear(), $this->getWeek(), $weekday);
		return $this;
	}
	
	/**
	 * @return int between 1 and 12
	 */
	public function getMonth()
	{
		return $this->getProperty('mon');
	}
	
	/**
	 * @param int
	 * @return Date
	 */
	public function setMonth($month)
	{
	    return $this->setProperty('mon', $month);
	}
	
	/**
	 * @return int
	 */
	public function getYear()
	{
		return $this->getProperty('year');
	}
	
	/**
	 * @param int
	 * @return Date
	 */
	public function setYear($year)
	{
	    return $this->setProperty('year', $year);
	}
	
	/**
	 * @return int
	 */
	public function getHour()
	{
	    return $this->getProperty('hours');
	}
	
	/**
	 * @param int $hour
	 * @return Date
	 */
	public function setHour($hour)
	{
	    return $this->setProperty('hours', $hour);
	}
	
	/**
	 * @return int
	 */
	public function getMinute()
	{
	    return $this->getProperty('minutes');
	}
	
	/**
	 * @param int $minute
	 * @return Date
	 */
	public function setMinute($minute)
	{
	    return $this->setProperty('minutes', $minute);
	}
	
	/**
	 * @return int
	 */
	public function getSecond()
	{
	    return $this->getProperty('seconds');
	}
	
	/**
	 * @param int $second
	 * @return Date
	 */
	public function setSecond($second)
	{
	    return $this->setProperty('seconds', $second);
	}
	
	/**
	 * Set default presentation format.
	 * 
	 * @param string $format
	 * @return Date
	 */
	public function setFormat($format)
	{
		$this->format = $format;
		return $this;
	}
	
	/**
	 * Get default presentation format
	 * 
	 * @return string
	 */
	public function getFormat()
	{
		return $this->format;
	}
	
	/**
	 * Format time as string. Use default format if not specified.
	 * 
	 * @param string $format optional
	 * @return string
	 */
	public function format($format = null)
	{
		if (!isset($format)) {
			$format = $this->format;
		}
		return date($format, $this->time);
	}
	
	/**
	 * Alter the timestamp. Accepts the same format as PHP's strotime().
	 * 
	 * @param string $description
	 * @return Date
	 */
	public function modify($description)
	{
		return new static(strtotime($description, $this->time));
	}
	
	/**
	 * @return string
	 */
	public function __toString()
	{
	    return $this->format();
	}
}