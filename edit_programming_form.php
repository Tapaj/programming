<?php

/**
 * Defines the editing form for the programming question type.
 *
 * @package    qtype_programming
 * @copyright  NA
 * @license    NA
 */


defined('MOODLE_INTERNAL') || die();


/**
 * programming question type editing form.
 *
 * @copyright  NA
 * @license    NA
 */
class qtype_programming_edit_form extends question_edit_form {

    /**
     * add question type specific form fields
     * 
     * @param object $mform is the form being built
     */
    protected function definition_inner($mform) {
        $qtype = question_bank::get_qtype('programming');

        
        // The "general" at the top and "tags" at the bottom are included by default

        // add the "test cases" section
        $this->add_per_answer_fields($mform, get_string('choiceno', 'qtype_multichoice', '{no}'), question_bank::fraction_options_full());


        // adds the "response option" section
        $mform->addElement('header', 'responseoptions', get_string('responseoptions', 'qtype_programming'));
        $mform->setExpanded('responseoptions');

            $mform->addElement('select', 'responseformat',
                    get_string('responseformat', 'qtype_programming'), $qtype->response_formats());
            $mform->setDefault('responseformat', 'monospaced');

            $mform->addElement('select', 'responserequired',
                    get_string('responserequired', 'qtype_programming'), $qtype->response_required_options());
            $mform->setDefault('responserequired', 1);
            $mform->disabledIf('responserequired', 'responseformat', 'eq', 'noinline');

            $mform->addElement('select', 'responsefieldlines',
                    get_string('responsefieldlines', 'qtype_programming'), $qtype->response_sizes());
            $mform->setDefault('responsefieldlines', 15);
            $mform->disabledIf('responsefieldlines', 'responseformat', 'eq', 'noinline');

            $mform->addElement('select', 'attachments',
                    get_string('allowattachments', 'qtype_programming'), $qtype->attachment_options());
            $mform->setDefault('attachments', 0);
            $mform->disabledIf('attachments', 'responserequired', 'eq', 1);

            $mform->addElement('select', 'attachmentsrequired',
                    get_string('attachmentsrequired', 'qtype_programming'), $qtype->attachments_required_options());
            $mform->setDefault('attachmentsrequired', 0);
            $mform->addHelpButton('attachmentsrequired', 'attachmentsrequired', 'qtype_programming');
            $mform->disabledIf('attachmentsrequired', 'attachments', 'eq', 0);

            $mform->addElement('filetypes', 'filetypeslist', get_string('acceptedfiletypes', 'qtype_programming'));
            $mform->addHelpButton('filetypeslist', 'acceptedfiletypes', 'qtype_programming');
            $mform->disabledIf('filetypeslist', 'attachments', 'eq', 0);

        // adds the "response template" section
        $mform->addElement('header', 'responsetemplateheader', get_string('responsetemplateheader', 'qtype_programming'));

            $mform->addElement('editor', 'responsetemplate', get_string('responsetemplate', 'qtype_programming'),
                    array('rows' => 10),  array_merge($this->editoroptions, array('maxfiles' => 0)));
            $mform->addHelpButton('responsetemplate', 'responsetemplate', 'qtype_programming');

        // adds the "grader information" section
        $mform->addElement('header', 'graderinfoheader', get_string('graderinfoheader', 'qtype_programming'));
            $mform->setExpanded('graderinfoheader');
            $mform->addElement('editor', 'graderinfo', get_string('graderinfo', 'qtype_programming'),
                    array('rows' => 10), $this->editoroptions);
    }

    /**
     * Add a set of form fields, obtained from get_per_answer_fields, to the form,
     * one for each existing answer, with some blanks for some new ones.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $minoptions the minimum number of answer blanks to display.
     *      Default QUESTION_NUMANS_START.
     * @param $addoptions the number of answer blanks to add. Default QUESTION_NUMANS_ADD.
     */
    protected function add_per_answer_fields(&$mform, $label, $gradeoptions,
            $minoptions = 1, $addoptions = 1) {
        $mform->addElement('header', 'answerhdr',
                    "Test Cases", '');
        $mform->setExpanded('answerhdr', 1);

        $mform->addElement("advcheckbox", "testcasevisible", get_string("testcasevisible", "qtype_programming"), "Enable", array(0, 1));

        $answersoption = '';
        $repeatedoptions = array();
        $repeated = $this->get_per_answer_fields($mform, $label, $gradeoptions,
                $repeatedoptions, $answersoption);

        if (isset($this->question->options)) {
            $repeatsatstart = count($this->question->options->$answersoption);
        } else {
            $repeatsatstart = $minoptions;
        }

        $this->repeat_elements($repeated, $repeatsatstart, $repeatedoptions,
                'noanswers', 'addanswers', $addoptions,
                $this->get_more_choices_string(), true);
    }

    /**
     * Get the list of form elements to repeat, one for each answer.
     * @param object $mform the form being built.
     * @param $label the label to use for each option.
     * @param $gradeoptions the possible grades for each answer.
     * @param $repeatedoptions reference to array of repeated options to fill
     * @param $answersoption reference to return the name of $question->options
     *      field holding an array of answers
     * @return array of form fields.
     */
    protected function get_per_answer_fields($mform, $label, $gradeoptions,
            &$repeatedoptions, &$answersoption) {
        $repeated = array();
        $label = "Input";
        $repeated[] = $mform->createElement('editor', 'answer',
                $label, array('rows' => 1), $this->editoroptions);
        $repeated[] = $mform->createElement('select', 'fraction',
                get_string('grade'), $gradeoptions);
        $repeated[] = $mform->createElement('editor', 'feedback',
                "Expected output", array('rows' => 1), $this->editoroptions);
        $repeatedoptions['answer']['type'] = PARAM_RAW;
        $repeatedoptions['fraction']['default'] = 0;
        $answersoption = 'answers';
        return $repeated;
    }

    protected function data_preprocessing($question) {
        $question = parent::data_preprocessing($question);

        if (empty($question->options)) {
            return $question;
        }

        $question->responseformat = $question->options->responseformat;
        $question->responserequired = $question->options->responserequired;
        $question->responsefieldlines = $question->options->responsefieldlines;
        $question->attachments = $question->options->attachments;
        $question->attachmentsrequired = $question->options->attachmentsrequired;
        $question->filetypeslist = $question->options->filetypeslist;

        $draftid = file_get_submitted_draft_itemid('graderinfo');
        $question->graderinfo = array();
        $question->graderinfo['text'] = file_prepare_draft_area(
            $draftid,                                           // Draftid
            $this->context->id,                                 // context
            'qtype_programming',                                // component
            'graderinfo',                                       // filarea
            !empty($question->id) ? (int) $question->id : null, // itemid
            $this->fileoptions,                                 // options
            $question->options->graderinfo                      // text
        );
        $question->graderinfo['format'] = $question->options->graderinfoformat;
        $question->graderinfo['itemid'] = $draftid;

        $question->responsetemplate = array(
            'text' => $question->options->responsetemplate,
            'format' => $question->options->responsetemplateformat,
        );

        return $question;
    }

    public function validation($fromform, $files) {
        $errors = parent::validation($fromform, $files);

        // Don't allow both 'no inline response' and 'no attachments' to be selected,
        // as these options would result in there being no input requested from the user.
        if ($fromform['responseformat'] == 'noinline' && !$fromform['attachments']) {
            $errors['attachments'] = get_string('mustattach', 'qtype_programming');
        }

        // If 'no inline response' is set, force the teacher to require attachments;
        // otherwise there will be nothing to grade.
        if ($fromform['responseformat'] == 'noinline' && !$fromform['attachmentsrequired']) {
            $errors['attachmentsrequired'] = get_string('mustrequire', 'qtype_programming');
        }

        // Don't allow the teacher to require more attachments than they allow; as this would
        // create a condition that it's impossible for the student to meet.
        if ($fromform['attachments'] != -1 && $fromform['attachments'] < $fromform['attachmentsrequired'] ) {
            $errors['attachmentsrequired']  = get_string('mustrequirefewer', 'qtype_programming');
        }

        return $errors;
    }

    public function qtype() {
        return 'programming';
    }
}
