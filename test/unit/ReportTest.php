<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2013-2021 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

declare(strict_types=1);

use oat\generis\test\TestCase;
use oat\oatbox\reporting\Report;

class ReportTest extends TestCase
{
    public function testConstructThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        new Report('foo', 'bar');
    }

    public function testStaticCallThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        Report::foo();
    }

    public function testStaticInstantiationThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        Report::createFoo();
    }

    public function testNonexistentMethodCallThrowsException(): void
    {
        $this->expectException(BadMethodCallException::class);
        $report = new Report(Report::TYPE_SUCCESS, 'message');
        $report->foo();
    }

    public function testFilterWrongTypesThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Type of report `foo` is unsupported');
        $report = new Report(Report::TYPE_SUCCESS, 'message');
        $report->getFoos();
    }

    public function testContainsWrongTypesThrowsException(): void
    {
        $this->expectException(OutOfBoundsException::class);
        $this->expectExceptionMessage('Type of report `foo` is unsupported');
        $report = new Report(Report::TYPE_SUCCESS, 'message');
        $report->containsFoo();
    }

    public function testToArray()
    {
        $report = Report::createInfo('foo', ['baz' => 'bar']);
        $report->add(Report::createWarning('foo', ['baz' => 'bar']));
        $expectedArray = [
            'type' => 'info',
            'message' => 'foo',
            'data' => ['baz' => 'bar'],
            'children' => [
                [
                    'type' => 'warning',
                    'message' => 'foo',
                    'data' => ['baz' => 'bar'],
                    'children' => [],
                ],
            ],
        ];
        $this->assertEquals($expectedArray, $report->toArray());
    }

    public function testBasicReport(): void
    {
        $report = new Report(Report::TYPE_SUCCESS, 'test message');

        self::assertFalse($report->hasChildren());
        self::assertEquals('test message', (string)$report);
        self::assertEquals('test message', $report->getMessage());
        self::assertEquals(Report::TYPE_SUCCESS, $report->getType());

        foreach ($report as $child) {
            self::fail('Should not contain children');
        }
    }

    public function testBasicReportWithChildren(): void
    {
        $sub1 = Report::createInfo('info31');
        $sub2 = Report::createError('error31');
        $sub3 = Report::createWarning('warning31');

        $report = new Report(Report::TYPE_SUCCESS, 'test message', null, [$sub1, $sub2, $sub3]);

        self::assertTrue($report->hasChildren());
        self::assertCount(3, $report->getChildren());
    }

    public function testDataInReport(): void
    {
        $exception = new Exception('testing');
        $report = new Report(Report::TYPE_INFO, 'test message2', $exception);

        self::assertFalse($report->hasChildren());
        self::assertEquals('test message2', (string)$report);
        self::assertEquals(Report::TYPE_INFO, $report->getType());

        foreach ($report as $child) {
            self::fail('Should not contain children');
        }

        self::assertSame($exception, $report->getData());
    }

    public function testNestedReport(): void
    {
        $report = new Report(Report::TYPE_WARNING, 'test message3');
        $sub1 = new Report(Report::TYPE_INFO, 'info31');
        $sub2 = new Report(Report::TYPE_ERROR, 'error31');
        $sub3 = new Report(Report::TYPE_WARNING, 'warning31');
        $report->add([$sub1, $sub2, $sub3]);

        self::assertTrue($report->hasChildren());
        self::assertEquals('test message3', (string)$report);
        self::assertEquals(Report::TYPE_WARNING, $report->getType());

        $array = [];
        foreach ($report as $child) {
            $array[] = $child;
        }
        self::assertCount(3, $array);

        [$first, $second, $third] = $array;

        self::assertFalse($first->hasChildren());
        self::assertEquals('info31', (string)$first);
        self::assertEquals(Report::TYPE_INFO, $first->getType());
        foreach ($first as $child) {
            self::fail('Should not contain children');
        }

        self::assertFalse($second->hasChildren());
        self::assertEquals('error31', (string)$second);
        self::assertEquals(Report::TYPE_ERROR, $second->getType());
        foreach ($second as $child) {
            self::fail('Should not contain children');
        }

        self::assertFalse($third->hasChildren());
        self::assertEquals('warning31', (string)$third);
        self::assertEquals(Report::TYPE_WARNING, $third->getType());
        foreach ($third as $child) {
            self::fail('Should not contain children');
        }

        self::assertFalse($report->contains(Report::TYPE_SUCCESS));
        self::assertFalse($report->containsSuccess());

        self::assertTrue($report->contains(Report::TYPE_INFO));
        self::assertTrue($report->containsInfo());

        self::assertTrue($report->contains(Report::TYPE_ERROR));
        self::assertTrue($report->containsError());

        self::assertTrue($report->contains(Report::TYPE_WARNING));
        self::assertTrue($report->containsWarning());
    }

    public function testJsonUnserialize(): void
    {
        $root = new Report(Report::TYPE_WARNING, 'test message3');
        $sub1 = new Report(Report::TYPE_INFO, 'info31');
        $sub2 = new Report(Report::TYPE_ERROR, 'error31');
        $subsub = new Report(Report::TYPE_SUCCESS, 'success31');

        // make report tree
        $sub1->add([$subsub]);
        $root->add([$sub1, $sub2]);

        $json = json_encode($root, JSON_PRETTY_PRINT);

        $report = Report::jsonUnserialize($json);

        self::assertTrue($report->hasChildren());
        self::assertEquals('test message3', (string)$report);
        self::assertEquals(Report::TYPE_WARNING, $report->getType());

        $array = [];
        foreach ($report as $child) {
            $array[] = $child;
        }
        self::assertCount(2, $array);
        [$first, $second] = $array;

        self::assertTrue($first->hasChildren());
        self::assertEquals('info31', (string)$first);
        self::assertEquals(Report::TYPE_INFO, $first->getType());
        foreach ($first as $child) {
            self::assertEquals('success31', (string)$child);
            self::assertEquals(Report::TYPE_SUCCESS, $child->getType());
        }

        self::assertFalse($second->hasChildren());
        self::assertEquals('error31', (string)$second);
        self::assertEquals(Report::TYPE_ERROR, $second->getType());
        foreach ($second as $child) {
            self::fail('Should not contain children');
        }

        self::assertTrue($report->contains(Report::TYPE_SUCCESS));
        self::assertTrue($report->contains(Report::TYPE_INFO));
        self::assertTrue($report->contains(Report::TYPE_ERROR));
    }

    public function testGetSuccessesAsFlat(): void
    {
        $report = new Report(Report::TYPE_INFO);

        $success_1 = new Report(Report::TYPE_SUCCESS, 'success_1');
        $success_1_1 = new Report(Report::TYPE_SUCCESS, 'success_1_1');
        $success_1->add($success_1_1);
        $success_2 = new Report(Report::TYPE_SUCCESS, 'success_2');

        $report->add($success_1);
        $report->add($success_2);

        $successes = (array)$report->getSuccesses(true);

        self::assertCount(3, $successes, '3 successes should be returned');
        self::assertEquals('success_1', (string)array_shift($successes));
        self::assertEquals('success_1_1', (string)array_shift($successes));
        self::assertEquals('success_2', (string)array_shift($successes));
    }

    public function testGetInfosAsFlat(): void
    {
        $report = new Report(Report::TYPE_SUCCESS);
        $info_1 = new Report(Report::TYPE_INFO, 'info_1');
        $info_1_1 = new Report(Report::TYPE_INFO, 'info_1_1');
        $info_1->add($info_1_1);
        $info_2 = new Report(Report::TYPE_INFO, 'info_2');

        $report->add($info_1);
        $report->add($info_2);

        $infos = (array)$report->getInfos(true);

        self::assertCount(3, $infos, '3 infos should be returned');
        self::assertEquals('info_1', (string)array_shift($infos));
        self::assertEquals('info_1_1', (string)array_shift($infos));
        self::assertEquals('info_2', (string)array_shift($infos));
    }

    public function testGetWarningsAsFlat(): void
    {
        $report = new Report(Report::TYPE_WARNING);
        $warning_1 = new Report(Report::TYPE_WARNING, 'warning_1');
        $warning_1_1 = new Report(Report::TYPE_WARNING, 'warning_1_1');
        $warning_1->add($warning_1_1);
        $warning_2 = new Report(Report::TYPE_WARNING, 'warning_2');

        $report->add($warning_1);
        $report->add($warning_2);

        $warnings = (array)$report->getWarnings(true);

        self::assertCount(3, $warnings, '3 warnings should be returned');
        self::assertEquals('warning_1', (string)array_shift($warnings));
        self::assertEquals('warning_1_1', (string)array_shift($warnings));
        self::assertEquals('warning_2', (string)array_shift($warnings));
    }

    public function testGetErrorsAsFlat(): void
    {
        $report = new Report(Report::TYPE_SUCCESS);
        $error_1 = new Report(Report::TYPE_ERROR, 'error_1');
        $error_1_1 = new Report(Report::TYPE_ERROR, 'error_1_1');
        $error_1->add($error_1_1);
        $error_2 = new Report(Report::TYPE_ERROR, 'error_2');

        $report->add($error_1);
        $report->add($error_2);

        $errors = (array)$report->getErrors(true);

        self::assertCount(3, $errors, '3 errors should be returned');
        self::assertEquals('error_1', (string)array_shift($errors));
        self::assertEquals('error_1_1', (string)array_shift($errors));
        self::assertEquals('error_2', (string)array_shift($errors));
    }

    public function testStaticReportCreation(): void
    {
        $info = Report::createInfo('info31');
        $success = Report::createSuccess('success31');
        $warning = Report::createWarning('warning31');
        $error = Report::createError('error31');

        self::assertEquals(Report::TYPE_INFO, $info->getType());
        self::assertEquals('info31', $info->getMessage());

        self::assertEquals(Report::TYPE_SUCCESS, $success->getType());
        self::assertEquals('success31', $success->getMessage());

        self::assertEquals(Report::TYPE_WARNING, $warning->getType());
        self::assertEquals('warning31', $warning->getMessage());

        self::assertEquals(Report::TYPE_ERROR, $error->getType());
        self::assertEquals('error31', $error->getMessage());
    }

    public function testFilterChildrenByTypes(): void
    {
        $report = new Report(Report::TYPE_INFO);

        $success = Report::createSuccess('success');
        $warning = Report::createWarning('warning');
        $error = Report::createError('error');

        $report->add($success)->add($success);
        $report->add($warning)->add($warning);
        $report->add($error)->add($error);

        $filtered = $report->filterChildrenByTypes([
            Report::TYPE_WARNING,
            Report::TYPE_ERROR
        ]);

        self::assertCount(4, $filtered);

        $output = [];
        foreach ($filtered as $r) {
            $output[$r->getType()][] = $r->getMessage();
        }

        self::assertCount(2, $output[Report::TYPE_ERROR]);
        self::assertCount(2, $output[Report::TYPE_WARNING]);
    }

    public function testCreateWithInterpolation(): void
    {
        $report = Report::create(
            Report::TYPE_ERROR,
            'my data %s',
            [
                'test'
            ]
        );

        $serialized = $report->jsonSerialize();

        /** @var Report $unSerialized */
        $unSerialized = Report::jsonUnserialize($serialized);

        self::assertSame(Report::TYPE_ERROR, $report->getType());
        self::assertSame('my data test', $report->getMessage());
        self::assertSame('my data %s', $serialized['interpolationMessage']);
        self::assertSame(['test'], $serialized['interpolationData']);

        self::assertSame(Report::TYPE_ERROR, $unSerialized->getType());
        self::assertSame('my data test', $unSerialized->getMessage());
        self::assertSame('my data test', $unSerialized->translateMessage());
    }

    public function testGetAllMessages(): void
    {
        $report = Report::createInfo('1')
            ->add(
                Report::createSuccess('2')
                    ->add(
                        Report::createWarning('3')
                    )
            );

        self::assertEquals('1, 2, 3', $report->getAllMessages());
    }
}
