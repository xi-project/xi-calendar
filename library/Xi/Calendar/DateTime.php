<?php
namespace Xi\Calendar;

use DateTime as NativeDateTime,
    DateTimeZone,
    DateInterval;

/**
 * An immutable DateTime object. Wraps a (mutable) native PHP DateTime and
 * attempts to maintain interface level compatibility with it to a degree.
 */
class DateTime
{
    /**
     * @constr string
     */
    const DEFAULT_FORMAT = self::ISO8601;
    
    /**
     * @const string Y-m-d\TH:i:sP
     */
    const ATOM  = NativeDateTime::ATOM;
    
    /**
     * @const string l, d-M-y H:i:s T
     */
    const COOKIE  = NativeDateTime::COOKIE;
    
    /**
     * @const string Y-m-d\TH:i:sO
     */
    const ISO8601  = NativeDateTime::ISO8601;
    
    /**
     * @const string D, d M y H:i:s O
     */
    const RFC822  = NativeDateTime::RFC822;
    
    /**
     * @const string l, d-M-y H:i:s T
     */
    const RFC850  = NativeDateTime::RFC850;
    
    /**
     * @const string D, d M y H:i:s O
     */
    const RFC1036  = NativeDateTime::RFC1036;
    
    /**
     * @const string D, d M Y H:i:s O
     */
    const RFC1123  = NativeDateTime::RFC1123;
    
    /**
     * @const string D, d M Y H:i:s O
     */
    const RFC2822  = NativeDateTime::RFC2822;
    
    /**
     * @const string Y-m-d\TH:i:sP
     */
    const RFC3339  = NativeDateTime::RFC3339;
    
    /**
     * @const string D, d M Y H:i:s O
     */
    const RSS = NativeDateTime::RSS;
    
    /**
     * @const string Y-m-d\TH:i:sP
     */
    const W3C = NativeDateTime::W3C;
    
    /**
     * @var NativeDateTime
     */
    private $datetime;
    
    /**
     * @var array getdate() output from timestamp
     */
    private $properties;
    
    /**
     * Accepts a unix timestamp, a string accepted by strtotime() or a native
     * PHP DateTime. Uses the default timezone unless a NativeDateTime with a
     * non-default DateTimeZone is provided.
     *
     * @param int|string|NativeDateTime $time optional
     */
    public function __construct($time = null)
    {
        if (null === $time) {
            $time = new NativeDateTime();
        } elseif (is_int($time)) {
            $time = new NativeDateTime("@$time");
        } elseif (!($time instanceof NativeDateTime)) {
            $time = new NativeDateTime($time);
        }
        $this->datetime = $time;
    }
    
    /**
     * Fluent alias for `new DateTime`
     * 
     * @param int|string|NativeDateTime $time optional
     * @return DateTime
     */
    public static function create($time = null)
    {
        return new static($time);
    }
    
    /**
     * Returns a new DateTime object formatted according to the specified
     * format.
     *
     * @param string $format
     * @param string $datetime
     * @param DateTimeZone $timezone optional
     */
    public static function createFromFormat($format, $datetime, $timezone = null)
    {
        return new static(NativeDateTime::createFromFormat($format, $datetime, $timezone));
    }
    
    /**
     * Create a string representation of the DateTime using the provided format
     *
     * @param string $format optional, defaults to ISO8601
     * @return string
     */
    public function format($format = self::ISO8601)
    {
        return $this->datetime->format($format);
    }
    
    /**
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }
    
    /**
     * Safely access the native PHP DateTime; result is a clone of the actual
     * one and modifications will not be reflected in this object.
     *
     * @return NativeDateTime
     */
    public function getDatetime()
    {
        return clone $this->datetime;
    }
    
    /**
     * @return int unix timestamp
     */
    public function getTimestamp()
    {
        return $this->datetime->getTimestamp();
    }
    
    /**
     * @return int timezone offset
     */
    public function getOffset()
    {
        return $this->datetime->getOffset();
    }
    
    /**
     * @return DateTimeZone
     */
    public function getTimezone()
    {
        return $this->datetime->getTimezone();
    }
    
    /**
     * @return int between 1 and 31
     */
    public function getDayOfMonth()
    {
        return $this->getProperty('mday');
    }
    
    /**
     * @return int between 1 and 31
     */
    public function getDaysInMonth()
    {
        $properties = $this->getProperties();
        return cal_days_in_month(CAL_GREGORIAN, $properties['mon'], $properties['year']);
    }
    
    /**
     * @return int between 1 and 366
     */
    public function getDayOfYear()
    {
        return $this->getProperty('yday');
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
     * @return int between 1 and 7 (starts from monday)
     */
    public function getDayOfWeek()
    {
        return $this->getProperty('wday');
    }
    
    /**
     * @return int between 1 and 12
     */
    public function getMonth()
    {
        return $this->getProperty('mon');
    }
    
    /**
     * @return int
     */
    public function getYear()
    {
        return $this->getProperty('year');
    }
    
    /**
     * @return int
     */
    public function getHour()
    {
        return $this->getProperty('hours');
    }
    
    /**
     * @return int
     */
    public function getMinute()
    {
        return $this->getProperty('minutes');
    }
    
    /**
     * @return int
     */
    public function getSecond()
    {
        return $this->getProperty('seconds');
    }
    
    /**
     * Get properties of current date. Similar to PHP's getdate() but weekdays
     * and days of the year start from 1.
     * 
     * Output is cached; safe to call multiple times.
     *
     * @return array
     */
    protected function getProperties()
    {
        if (null === $this->properties) {
            $p = getdate($this->getTimestamp());
            
            // Weekdays start from monday as 1
            if (!$p['wday'])
            {
                $p['wday'] = 7;
            }
        
            // Day of year starts not from 0 but 1
            $p['yday']++;
            
            $this->properties = $p;
        }
        return $this->properties;
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
     * Get a new DateTime with the specified year, month and day. Does not
     * modify hour, minute and second components.
     * 
     * @param int $year
     * @param int $month
     * @param int $day
     * @return DateTime
     */
    public function withDate($year, $month, $day)
    {
        return $this->withModification(function($datetime) use($year, $month, $day) {
            $datetime->setDate($year, $month, $day);
        });
    }
    
    /**
     * Get a new DateTime with the specified ISO 8601 year, week and day. Does
     * not modify hour, minute and second components.
     * 
     * @param int $year
     * @param int $week
     * @param int $day optional, defaults to 1 (monday)
     * @return DateTime
     */
    public function withISODate($year, $week, $day = 1)
    {
        return $this->withModification(function($datetime) use($year, $week, $day) {
            $datetime->setISODate($year, $week, $day);
        });
    }
    
    /**
     * Get a new DateTime with the specified hour, minute and second. Does not
     * modify year, week and day components.
     *
     * @param int $hour
     * @param int $minute
     * @param int $second optional, defaults to 0
     * @return DateTime
     */
    public function withTime($hour, $minute, $second = 0)
    {
        return $this->withModification(function($datetime) use($hour, $minute, $second) {
            $datetime->setTime($hour, $minute, $second);
        });
    }
    
    /**
     * Get a new DateTime with the given DateInterval or integer (seconds)
     * subtracted.
     * 
     * @param int|DateInterval $interval
     * @return DateTime
     */
    public function withSubtraction($interval)
    {
        $interval = $this->toInterval($value);
        return $this->withModification(function($datetime) use($interval) {
            $datetime->sub($interval);
        });
    }
    
    /**
     * Get a new DateTime with the given DateInterval or integer (seconds)
     * added.
     * 
     * @param int|DateInterval $interval
     * @return DateTime
     */
    public function withAddition($interval)
    {
        $interval = $this->toInterval($value);
        return $this->withModification(function($datetime) use($interval) {
            $datetime->add($interval);
        });
    }
    
    /**
     * Create a new DateTime object by incrementing or decrementing in a format
     * accepted by strtotime().
     * 
     * @param string $modification
     * @return DateTime
     */
    public function modify($modification)
    {
        return $this->withModification(function($datetime) use($modification) {
            $datetime->modify($modification);
        });
    }
    
    /**
     * Returns the difference between two DateTime objects
     * 
     * @param NativeDateTime|DateTime $other
     * @return DateInterval
     */
    public function diff($other)
    {
        if ($other instanceof self) {
            $other = $other->getDatetime();
        }
        return $this->datetime->diff($other);
    }
    
    /**
     * @param int $year
     * @return DateTime
     */
    public function withYear($year)
    {
        return $this->withModifiedProperty('year', $year);
    }
    
    /**
     * @param int $yday
     * @return DateTime
     */
    public function withDayOfYear($yday)
    {
        return $this->withProperties(array(
            'mon' => 1,
            'mday' => $yday
        ));
    }
    
    /**
     * @param int $month
     * @return DateTime
     */
    public function withMonth($month)
    {
        return $this->withModifiedProperty('mon', $month);
    }
    
    /**
     * @param int $dayOfMonth
     * @return DateTime
     */
    public function withDayOfMonth($dayOfMonth)
    {
        return $this->withModifiedProperty('mday', $dayOfMonth);
    }
    
    /**
     * With modified ISO 8601 week number
     * 
     * @param int $wnumber
     * @return Date
     */
    public function withWeek($wnumber)
    {
        return $this->withISODate($this->getYear(), $wnumber, $this->getDayOfWeek());
    }
    
    /**
     * @param int $weekday 1 (monday) through 7 (sunday)
     * @return DateTime
     */
    public function withDayOfWeek($weekday)
    {
        return $this->withISODate($this->getYear(), $this->getWeek(), $weekday);
    }
    
    /**
     * @param int $hour
     * @return DateTime
     */
    public function withHour($hour)
    {
        return $this->withModifiedProperty('hours', $hour);
    }
    
    /**
     * @param int $minute
     * @return DateTime
     */
    public function withMinute($minute)
    {
        return $this->withModifiedProperty('minutes', $minute);
    }
    
    /**
     * @param int $second
     * @return DateTime
     */
    public function withSecond($second)
    {
        return $this->withModifiedProperty('seconds', $second);
    }
    
    /**
     * Creates a new DateTime with the given modification applied to the
     * current native PHP DateTime object.
     *
     * @param callback(NativeDateTime) $modification
     * @return DateTime
     */
    protected function withModification($modification)
    {
        $datetime = clone $this->datetime;
        $modification($datetime);
        return new static($datetime);
    }
    
    /**
     * @param int|DateInterval $value
     * @return DateInterval
     */
    protected function toInterval($value)
    {
        if (!($interval instanceof DateInterval)) {
            $interval = DateInterval::createFromDateString((int) $interval + ' seconds');
        }
        return $interval;
    }
    
    /**
     * @param string $property
     * @param int $value
     * @return DateTime
     */
    protected function withModifiedProperty($property, $value)
    {
        return $this->withModifiedProperties(function($p) use($property, $value) {
            $p[$property] = $value;
            return $p;
        });
    }
    
    /**
     * @param callback($properties) $modification
     * @return DateTime
     */
    protected function withModifiedProperties($modification)
    {
        $properties = $this->getProperties();
        return $this->withProperties($modification($properties));
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
    protected function withProperties($properties)
    {
        return new static($this->propertiesToTimestamp($properties + $this->getProperties()));
    }
    
    /**
     * @param array $properties
     * @return int
     */
    protected function propertiesToTimestamp($properties)
    {
        return mktime(
            $properties['hours'],
            $properties['minutes'],
            $properties['seconds'],
            $properties['mon'],
            $properties['mday'],
            $properties['year']
        );
    }
}