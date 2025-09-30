<?php
/**
 * Script to fix date handling in all models
 */

// Models and their date fields that need to be fixed
$modelsToFix = [
    'Asset.php' => ['purchase_date', 'warranty_date'],
    'Award.php' => ['award_date'],
    'Announcement.php' => ['start_date', 'end_date'],
    'Complaint.php' => ['complaint_date'],
    'Event.php' => ['start_date', 'end_date'],
    'EmployeeImmigration.php' => ['issue_date', 'expiry_date'],
    'EmployeeQualificaiton.php' => ['start_date', 'end_date'],
    'EmployeeWorkExperience.php' => ['start_date', 'end_date'],
    'FinanceDeposit.php' => ['deposit_date'],
    'FinanceExpense.php' => ['expense_date'],
    'FinanceTransaction.php' => ['expense_date', 'deposit_date'],
    'FinanceTransfer.php' => ['date'],
    'Holiday.php' => ['start_date', 'end_date'],
    'Invoice.php' => ['invoice_date', 'due_date'],
    'JobCandidate.php' => ['application_date'],
    'JobInterview.php' => ['interview_date'],
    'JobPost.php' => ['start_date', 'end_date'],
    'Meeting.php' => ['meeting_date'],
    'OfficialDocument.php' => ['date'],
    'Payslip.php' => ['pay_period_month'],
    'Policy.php' => ['start_date', 'end_date'],
    'Project.php' => ['start_date', 'end_date'],
    'ProjectBug.php' => ['bug_date'],
    'ProjectDiscussion.php' => ['discussion_date'],
    'ProjectFile.php' => ['file_date'],
    'Promotion.php' => ['promotion_date'],
    'Resignation.php' => ['resignation_date'],
    'SalaryLoan.php' => ['date'],
    'SupportTicket.php' => ['date'],
    'Task.php' => ['start_date', 'end_date'],
    'TaskDiscussion.php' => ['discussion_date'],
    'TaskFile.php' => ['file_date'],
    'Termination.php' => ['termination_date'],
    'TicketComments.php' => ['comment_date'],
    'TrainingList.php' => ['start_date', 'end_date'],
    'Transfer.php' => ['transfer_date'],
    'Travel.php' => ['start_date', 'end_date'],
    'Warning.php' => ['warning_date'],
    'User.php' => ['date_of_birth']
];

$baseDir = 'app/Models/';

echo "Starting to fix date models...\n";
echo "==============================\n\n";

foreach ($modelsToFix as $modelFile => $dateFields) {
    $filePath = $baseDir . $modelFile;

    if (!file_exists($filePath)) {
        echo "⚠️  File not found: $filePath\n";
        continue;
    }

    echo "🔧 Fixing: $modelFile\n";

    $content = file_get_contents($filePath);

    // Add DateHelper import if not present
    if (strpos($content, 'use App\Helpers\DateHelper;') === false) {
        $content = str_replace(
            'use Carbon\Carbon;',
            "use App\Helpers\DateHelper;\nuse Carbon\Carbon;",
            $content
        );
    }

    // Fix each date field
    foreach ($dateFields as $field) {
        $setterMethodName = 'set' . str_replace('_', '', ucwords($field, '_')) . 'Attribute';
        $getterMethodName = 'get' . str_replace('_', '', ucwords($field, '_')) . 'Attribute';

        // Pattern to match old setter method
        $oldSetterPattern = "/public function {$setterMethodName}\(\$value\)\s*\{[^}]*Carbon::createFromFormat[^}]*\}/s";
        $newSetter = "public function {$setterMethodName}(\$value)\n\t{\n\t\t\$this->attributes['{$field}'] = DateHelper::parseToddmmyyyy(\$value);\n\t}";

        // Pattern to match old getter method
        $oldGetterPattern = "/public function {$getterMethodName}\(\$value\)\s*\{[^}]*Carbon::parse[^}]*\}/s";
        $newGetter = "public function {$getterMethodName}(\$value)\n\t{\n\t\treturn \$value;\n\t}";

        // Replace setter
        if (preg_match($oldSetterPattern, $content)) {
            $content = preg_replace($oldSetterPattern, $newSetter, $content);
            echo "  ✅ Fixed setter for: $field\n";
        }

        // Replace getter
        if (preg_match($oldGetterPattern, $content)) {
            $content = preg_replace($oldGetterPattern, $newGetter, $content);
            echo "  ✅ Fixed getter for: $field\n";
        }
    }

    // Write back to file
    file_put_contents($filePath, $content);
    echo "  💾 Saved: $modelFile\n\n";
}

echo "✨ All date models have been fixed!\n";
echo "All models now use DateHelper for consistent dd-mm-yyyy format handling.\n";
?>