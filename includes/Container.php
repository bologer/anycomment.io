<?php

namespace AnyComment;

use AnyComment\Repository\CommentRepository;
use AnyComment\Repository\CommentRepositoryInterface;
use AnyComment\Repository\UserRepository;
use AnyComment\Repository\UserRepositoryInterface;
use DI\ContainerBuilder;

class Container {
	public function build() {
		$builder = new ContainerBuilder();
		$builder->addDefinitions( [
			CommentRepositoryInterface::class => CommentRepository::class,
			UserRepositoryInterface::class    => UserRepository::class
		] );

		return $builder->build();
	}
}
