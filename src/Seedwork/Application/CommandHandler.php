<?php

declare(strict_types=1);

namespace Seedwork\Application;

/**
 * Marker interface for write-only application use cases (command handlers).
 *
 * This interface declares no methods. Concrete use case interfaces extend it and
 * declare execute(YourCommand $command): void. The generic documents the command
 * type for static analysis and DI.
 *
 * Conventions:
 * - One use case interface per write operation (e.g. CreateNewRestaurant).
 *   Interface extends CommandHandler<YourCommand> and declares execute(YourCommand): void.
 * - Command DTOs use only primitive types; no domain types. Handler builds
 *   domain types (e.g. EntityId::fromString($command->id)) when calling domain/repositories.
 * - Handler class implements the use case interface and is named <UseCase>Handler.
 *
 * @template TCommand of object
 */
interface CommandHandler
{
}
