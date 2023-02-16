<?php
declare(strict_types = 1);

namespace cusodede\history\models;

use yii\base\Model;

/**
 * Class TimelineEntry
 *
 * @property string $time
 * @property string $caption
 * @property string $content
 * @property int $user
 */
class TimelineEntry extends Model {
	public ?string $time;
	public ?string $caption;
	public ?string $content;
	public ?int $user;
}