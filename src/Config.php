<?php declare(strict_types = 1);

namespace Neicod\ObjectManager;

use Zend\ConfigAggregator\ConfigAggregator;

class Config implements ConfigInterface
{
    const OPTION_PREFERENCE = 'preference';
    const OPTION_PARAMETERS = 'parameters';
    const OPTION_SHARED = 'shared';

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @var array
     */
    private $data = [];

    /**
     * Config constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->configure($data);
    }

    /**
     * @inheritDoc
     */
    public function configure(array $options): void
    {
        foreach ($options as $name => $option) {
            if (isset($option[self::OPTION_PREFERENCE])) {
                $this->aliases[$name] = $option[self::OPTION_PREFERENCE];
                unset($option[self::OPTION_PREFERENCE]);
            }
            if (empty($option)) {
                continue;
            }
            $data = $this->data[$name]?? [];
            $config = new ConfigAggregator([
                function () use ($data) {
                    return $data;
                },
                function () use ($option) {
                    $result = [];
                    if (isset($option[self::OPTION_PARAMETERS])){
                        $result[self::OPTION_PARAMETERS] = $option[self::OPTION_PARAMETERS];
                    }
                    if (isset($option[self::OPTION_SHARED])){
                        $result[self::OPTION_SHARED] = $option[self::OPTION_SHARED];
                    }
                    return $result;
                }
            ]);
            $this->data[$name] = $config->getMergedConfig();
        }
    }

    /**
     * @param string $name
     * @return array
     */
    public function get(string $name): array {
        return $this->data[$name]?? [];
    }

    /**
     * @inheritDoc
     */
    public function resolveName(string $name): string
    {
        return $this->aliases[$name]?? $name;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function isShared(string $name): bool{
        return (($this->data[$name][self::OPTION_SHARED]?? false) === true);
    }
}
