<!DOCTYPE html>
<html>
<head>
    <title>Many To Many</title>
</head>
<body>

<h2>Students & Courses</h2>

@foreach($students as $student)
    <h3>{{ $student->name }}</h3>
    <ul>
        @foreach($student->courses as $course)
            <li>{{ $course->title }}</li>
        @endforeach
    </ul>
@endforeach

</body>
</html>
