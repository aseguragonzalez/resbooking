<?php

declare(strict_types=1);

namespace Seedwork\Application;

/**
 * Marker interface for read-only application use cases (query handlers).
 *
 * This interface declares no methods. Concrete use case interfaces extend it and
 * declare execute(YourQuery $query): YourResult. The generics document the query
 * and result types for static analysis and DI.
 *
 * Conventions:
 * - One use case interface per read operation (e.g. GetRestaurantById). Interface
 *   extends QueryHandler<YourQuery, YourResult> and declares execute(YourQuery): YourResult.
 * - Query DTOs use only primitive types; handler builds domain types when calling
 *   repositories or domain services.
 * - Handler class implements the use case interface and is named <UseCase>Handler.
 *
 * @template TQuery of object
 * @template TResult
 */
interface QueryHandler
{
}
