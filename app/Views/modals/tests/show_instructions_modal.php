<!-- Show Instructions Modal -->
<!-- Added By @PrachiP -->
<div id="instructions" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title"><?= $title; ?></h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                    <ul>
                        <li>Please make sure there is enough vertical spacing between two questions to avoid mixing of questions</li>
                        <li>Please make sure that no question is part of two different pages. Any question should occupy maximum one page.</li>
                        <li>Question prefix is the character that you have before the question number i.e. if your format is Q1. ,Q2. then prefix is 'Q'</li>
                        <li>Question suffix is the character that you have after the question number i.e. if your format is Q1. ,Q2. then suffix is '.'</li>
                        <li>If you want to also add solutions below the questions, you can do so by adding solution by leaving some whitespace below the question.
                            Then you can start writing the solution from prefix ':Solution:'. Please refer the template for more info.
                        </li>
                        <li>Options can be added just below the question text preferrably in a vertical manner</li>
                        <li>Please Download the word template to understand the format clearly. You can edit the same template to add your own questions and export to PDF.</li>


                    </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-dark" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
     