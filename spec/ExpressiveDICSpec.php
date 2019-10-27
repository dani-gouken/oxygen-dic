<?php
use ExpressiveDIC\DIC;
use ExpressiveDIC\Exceptions\NotFoundException;
use Psr\Container\ContainerInterface;
use \ExpressiveDIC\Exceptions\InvalidArgumentException;

class Fake{
    public $id;
    public function __construct()
    {
        $this->id = uniqid();
    }
    public function getId(){
        return 'FakeId'.$this->id;
    }
}

class Foo{
    public $id;
    public function __construct(Fake $fake,Int $index)
    {
        $this->id = uniqid();
        $this->fake = $fake;
        $this->index = $index;
    }

}

describe(DIC::class,function(){
    beforeEach(function(){
        $this->dic = new DIC();
    });

   it('should implement psr container interface',function(){
       expect($this->dic)->toBeAnInstanceOf(ContainerInterface::class);
   }) ;

    describe("DIC::describe",function(){
        it('should store description using callable',function(){
            $fake = new Fake();
            $this->dic->describe('foo',function()use($fake){
                return $fake;
            });

            expect($this->dic->getDescriptions())->toHaveLength(1);
            expect($this->dic->getDescriptions()['foo']()->id)->toBe($fake->id);
            expect($this->dic->getDescriptions()['foo']())->toBeAnInstanceOf(Fake::class);

        });
    });
    describe('DIC::describeMany',function(){
        it('should store description using array of callback',function(){
            $foo = new Fake();
            $bar = new Fake();

            $this->dic->describeMany([
                'foo'=>function() use ($foo) {
                    return $foo;
                },
                'bar'=>function() use ($bar) {
                    return $bar;
                }
            ]);
            expect($this->dic->getDescriptions())->toHaveLength(2);
            expect($this->dic->getDescriptions()['foo']()->id)->toBe($foo->id);
            expect($this->dic->getDescriptions()['bar']()->id)->toBe($bar->id);

        });
    });

    describe("DIC::describeValue",function(){
        it('should store a value',function(){
            $fake = new Fake();
            $this->dic->describeValue('foo',$fake);

            expect($this->dic->getValues())->toHaveLength(1);
            expect($this->dic->getValues()['foo']->id)->toBe($fake->id);
            expect($this->dic->getValues()['foo'])->toBe($fake);
        });
    });
    describe("DIC::describeValues",function(){
        it('should store values',function(){
            $foo = new Fake();
            $bar = new Fake();

            $this->dic->describeValues(['foo'=>$foo,'bar'=>$bar]);
            expect($this->dic->getValues())->toHaveLength(2);
            expect($this->dic->getValues()['foo']->id)->toBe($foo->id);
            expect($this->dic->getValues()['bar']->id)->toBe($bar->id);

        });

        it('should throw an exception if the value is not valid',function(){
            expect(function(){
                $this->dic->describeValues([
                    'foo'=>function(){
                        return false;
                    },
                ]);
            })->toThrow(new InvalidArgumentException);
        });
    });
    describe("DIC::describeSingleton",function(){
        it('should store a singleton',function(){
            $this->dic->describeSingleton('foo',function(){
                return new fake;
            });

            expect($this->dic->getSingletons())->toHaveLength(1);
            expect($this->dic->getSingletons()['foo']())->toBeAnInstanceOf(Fake::class);

        });
    });
    describe("DIC::describeSingletons",function(){
        it('should store singletons',function(){
            $this->dic->describeSingletons([
                'foo'=>function(){
                    return new Fake();
                },
                'bar'=>function(){
                    return new Fake();
                }
            ]);

            expect($this->dic->getSingletons())->toHaveLength(2);
            expect($this->dic->getSingletons()['foo']())->toBeAnInstanceOf(Fake::class);
            expect($this->dic->getSingletons()['bar']())->toBeAnInstanceOf(Fake::class);

        });
    });

    describe("DIC::hasValue",function(){
        it('should check if the value exists',function(){
            $foo = new Fake();
            $this->dic->describeValue('foo',$foo);

            expect($this->dic->hasValue('foo'))->toBe(true);
            expect($this->dic->hasValue('bar'))->toBe(false);
        });
    });
    describe("DIC::hasDescription",function(){
        it('should check if the description exists',function(){
            $this->dic->describe('foo',function(){
                return new Fake();
            });
            expect($this->dic->hasDescription('foo'))->toBe(true);
            expect($this->dic->hasDescription('bar'))->toBe(false);
        });
    });
    describe("DIC::hasSingleton",function(){
        it('should check if the singleton exists',function(){
            $this->dic->describeSingleton('foo',function(){
                return new Fake();
            });
            expect($this->dic->hasSingleton('foo'))->toBe(true);
            expect($this->dic->hasSingleton('bar'))->toBe(false);
        });
    });
    describe('DIC::has',function(){
       it('should check if a value with the given alias exist',function(){
           $this->dic->describe('foo',function(){
              return new Fake();
           });
           $this->dic->describeSingleton('bar',function(){
               return new Fake();
           });
           $this->dic->describeValue('baz',new Fake());
           expect($this->dic->has('foo')&&$this->dic->has('bar')&&$this->dic->has('baz'))->toBe(true);
           expect($this->dic->has('fake'))->toBe(false);
       });
    });

    describe('DIC::getSingleton',function(){
        it('should resolve a singleton or throw if it is not available',function(){
            expect(function(){$this->dic->getSingleton('foo');})->toThrow(new NotFoundException());
            $this->dic->describeSingleton('foo',function(){
                return new Fake();
            });
            expect($this->dic->getSingleton('foo'))->toBeAnInstanceOf(Fake::class);
        });

        it('should return the same instance',function(){
           $this->dic->describeSingleton('foo',function(){
               return new Fake();
           });
           $instance1 = $this->dic->getSingleton('foo');
           $instance2 = $this->dic->getSingleton('foo');
           expect($instance1->id)->toBe($instance2->id);
        });
    });
    describe('DIC::getValue',function(){
        it('should resolve an instance or throw it is not available',function(){
            expect(function(){$this->dic->getSingleton('foo');})->toThrow(new NotFoundException());
            $this->dic->describeValue('foo',new Fake());
            expect($this->dic->getValue('foo'))->toBeAnInstanceOf(Fake::class);
        });
    });
    describe('DIC::getDescription',function(){
        it('should resolve a description or throw it is not available',function(){
            expect(function(){
                $this->dic->getDescription('foo');
            })->toThrow(new NotFoundException());
            $this->dic->describe('foo',function(){
                return new Fake();
            });
            expect($this->dic->getDescription('foo'))->toBeAnInstanceOf(Fake::class);
        });
    });

    describe('DIC::get',function(){
        it('should resolve an available  or throw it is not available',function(){
            expect(function(){$this->dic->getDescription('foo');})->toThrow(new NotFoundException());
            $this->dic->describe('foo',function(){
                return new Fake();
            });
            expect($this->dic->getDescription('foo'))->toBeAnInstanceOf(Fake::class);
        });
    });

    it('should implements arrayAccess',function(){
        $this->dic['foo'] = function(){
            return new Fake();
        };
        expect($this->dic['foo'])->toBeAnInstanceOf(Fake::class);
        $instance1 = $this->dic['foo'];
        $instance2 = $this->dic['foo'];

        expect($instance1->id === $instance2->id)->toBeFalsy();
        expect(function(){
            $this->dic['bar'];
        })->toThrow(new NotFoundException());
        expect(function(){
            $this->dic['singleton::foo'];
        })->toThrow(new NotFoundException());
        $this->dic['value::foo'] = 42;
        expect($this->dic['description::foo'])->toBeAnInstanceOf(Fake::class);
        expect($this->dic["value::foo"])->toBe(42);

        $this->dic['value::foo'] = 24;
        expect($this->dic['value::foo'])->toBe(24);

        $this->dic['singleton::baz'] = function(){
            return new Fake();
        };
        expect($this->dic['baz'])->toBe($this->dic['singleton::baz']);
        expect($this->dic['value::baz'])->toBe($this->dic['singleton::baz']);
        expect(function(){
            $this->dic['description::baz'];
        })->toThrow(new NotFoundException());

        $instance1 = $this->dic['baz'];
        $instance2 = $this->dic['baz'];
        expect($instance1->id)->toBe($instance2->id);
    });

    it('should build an object',function(){
        expect($this->dic->make(Fake::class))->toBeAnInstanceOf(Fake::class);
    });

    it('should build an object with params',function(){
        $random = rand();
        expect($this->dic->make(Foo::class,['index'=>$random])->index)
            ->toBe($random);
    });

    it('should build an object and cache it',function(){
        $random = rand();
        expect($this->dic->make(Foo::class,['index'=>$random],true)->index)
                ->toBe($random);

        expect($this->dic->make(Foo::class)->index)
            ->toBe($random);
    });

    it('should build an object using values store in the container',function(){
        class Bar {
            public function __construct(Fake $baz){
                $this->id = $baz->id;
            }
        }

        $fake = new Fake();
        $this->dic->describe(Fake::class,function() use ($fake) {
            return $fake; 
        });
        expect($this->dic->make(Bar::class)->id)->toBe($fake->id);
    });


    it('should call a function',function(){
        function generateFakeId(Fake $fake){
            return 'fooId'.$fake->getId();
        }
        expect($this->dic->call('generateFakeId'))->toContain('fooId');
    });

    it('should call a function',function(){
        expect($this->dic->call('Fake::getId'))->toContain('FakeId');
    });
});
