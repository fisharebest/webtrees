# Custom Matching Rules

## Writing A Custom Rule

Writing a custom matching rule, say one that checks the authentication state, is a bit involved but not difficult.

1. Write a rule class that implements the RuleInterface.

2. Get the RuleIterator from the RouterContainer and `append()` or `prepend()` your new Rule. (You can also wrap it in a callable to make it lazy-loaded, such as with Aura.Di Lazy instances.)

3. Use the Matcher as normal.

Here is a naive rule that checks to see if the request has a particular header set; the rule passes if it is, and fails if it does not.  The header value is captured into the route attributes.

```php
<?php
use Aura\Router\Route;
use Aura\Router\Rule\RuleInterface;
use Psr\Http\Message\ServerRequestInterface;

class ApiVersionRule implements RuleInterface
{
    public function __invoke(ServerRequestInterface $request, Route $route)
    {
        $versions = $request->getHeader('X-Api-Version');
        if (count($versions) !== 1) {
            return false;
        }

        $route->attributes(['apiVersion' => $versions[0]]);
        return true;
    }
}
?>
```

You can then `prepend()` or `append()` the rule to the _RuleIterator_ from the _RouterContainer_. (Prepended rules get run first, appended ones last.)

```php
<?php
$ruleIterator = $routerContainer->getRuleIterator();
$ruleIterator->append(new ApiVersionRule());
?>
```

Then you can run the _Matcher_ as normal, and your new rule will be honored.

```php
<?php
$matcher = $routerContainer->getMatcher();
$route = $matcher->match($request);
?>
```

## Setting Rules

If you want, you can set all the matching rules into the _RouterContainer_ in advance. They will be injected into the _RuleIterator_ and thereby into the _Matcher_ automatically.

For good or bad, you need to pass the entire set of rules, not just your custom ones. This is because you need to include not just your own rules, but the default ones as well. It would look something like this:

```php
use Aura\Router\Rule;

$routerContainer->getRuleIterator()->set([
    // default rules
    new Rule\Secure(),
    new Rule\Host(),
    new Rule\Path(),
    new Rule\Allows(),
    new Rule\Accepts(),
    new Rule\Special(),
    // custom rule
    new ApiVersionRule()
]);
```

> N.b. You can review the `RouterContainer::getRules()` method to see the default rule set.

Setting all the rules at once means you can place your rule exactly where you want.  If you want your custom rule to go right in the middle of the default set, you could do something like this:

```php
use Aura\Router\Rule;

$routerContainer->getRuleIterator()->set([
    new Rule\Secure(),
    new Rule\Host(),
    new ApiVersionRule(), // custom rule in the middle
    new Rule\Path(),
    new Rule\Allows(),
    new Rule\Accepts(),
    new Rule\Special(),
]);
```

Finally, if you feel you don't need some rules, you can omit them from the list. For example, you may only care about path and HTTP method matching:

```php
use Aura\Router\Rule;

$routerContainer->getRuleIterator()->set([
    new Rule\Path(),
    new Rule\Allows(),
]);
```
