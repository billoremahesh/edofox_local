<?php
 echo "<h4 class='text-center text-muted'>ABSENT STUDENTS</h4>";
if(!empty($test_absent_students)):
?>
    <table id="absent-students-table" class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Roll No.</th>
                <th>Name</th>
                <th>Category</th>
                <th>Mobile</th>
                <th>Parent Mob</th>
                <th>Classroom</th>
            </tr>
        </thead>
        <tbody>
        <?php
            $row_count = 1;
            foreach($test_absent_students as $row):
                $roll_no = $row["roll_no"];
                $student_name = $row["name"];
                $caste_category = $row["caste_category"];
                $mobile_no = $row["mobile_no"];
                $parent_mobile_no = $row["parent_mobile_no"];
                $package_name = $row['package_name'];
        ?>
            <tr>
                <td><?= $row_count ?></td>
                <td><?= $roll_no ?></td>
                <td><?= $student_name ?></td>
                <td><?= $caste_category ?></td>
                <td><?= $mobile_no ?></td>
                <td><?= $parent_mobile_no ?></td>
                <td><?= $package_name ?></td>
            </tr>
        <?php
            $row_count++;
            endforeach;
        ?>
        </tbody>
    </table>
<?php
else:
    echo "<p>No Absent students.</p>";
endif;
?>