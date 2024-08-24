document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('course_section_s').addEventListener('change', function() {
        var classId = this.options[this.selectedIndex].getAttribute('data-id');

        // alert(classId);
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'masterlist.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                document.getElementById('adviser').value = xhr.responseText;
            }
        };
        xhr.send('class_id=' + encodeURIComponent(classId));
    });
});