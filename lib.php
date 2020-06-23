<?php
/**
 * Serve question type files
 *
 * @package    qtype_programming
 * @copyright  NA
 * @license    NA
 */


defined('MOODLE_INTERNAL') || die();


/**
 * Checks file access for programming questions.
 *
 * @package  qtype_programming
 * @category files
 * @param stdClass $course course object
 * @param stdClass $cm course module object
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return bool
 */

function qtype_programming_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options=array()) {
    global $CFG;
    require_once($CFG->libdir . '/questionlib.php');
    question_pluginfile($course, $context, 'qtype_programming', $filearea, $args, $forcedownload, $options);
}
