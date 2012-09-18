<?php

use Mockery as m;
use Illuminate\Pagination\Environment;

class EnvironmentTest extends PHPUnit_Framework_TestCase {

	public function tearDown()
	{
		m::close();
	}


	public function testCreationOfEnvironment()
	{
		$env = $this->getEnvironment();
	}


	public function testPaginatorCanBeCreated()
	{
		$env = $this->getEnvironment();
		$request = Symfony\Component\HttpFoundation\Request::create('http://foo.com', 'GET');
		$env->setRequest($request);
		
		$this->assertInstanceOf('Illuminate\Pagination\Paginator', $env->make(array('foo', 'bar'), 2, 2));
	}


	public function testPaginationViewCanBeCreated()
	{
		$env = $this->getEnvironment();
		$paginator = m::mock('Illuminate\Pagination\Paginator');
		$env->getViewDriver()->shouldReceive('make')->once()->with('pagination::slider', array('environment' => $env, 'paginator' => $paginator))->andReturn('foo');

		$this->assertEquals('foo', $env->getPaginationView($paginator));
	}


	public function testCurrentPageCanBeRetrieved()
	{
		$env = $this->getEnvironment();
		$request = Symfony\Component\HttpFoundation\Request::create('http://foo.com?page=2', 'GET');
		$env->setRequest($request);

		$this->assertEquals(2, $env->getCurrentPage());
	}


	public function testRootUrlCanBeRetrieved()
	{
		$env = $this->getEnvironment();
		$request = Symfony\Component\HttpFoundation\Request::create('http://foo.com?page=2', 'GET');
		$env->setRequest($request);

		$this->assertEquals('http://foo.com', $env->getRootUrl());		
	}


	protected function getEnvironment()
	{
		$request = m::mock('Symfony\Component\HttpFoundation\Request');
		$view = m::mock('Illuminate\View\Environment');
		$trans = m::mock('Symfony\Component\Translation\TranslatorInterface');

		$view->shouldReceive('addNamespace')->once()->with('pagination', realpath(__DIR__.'/../src/Illuminate/Pagination/views'));

		return new Environment($request, $view, $trans);
	}

}