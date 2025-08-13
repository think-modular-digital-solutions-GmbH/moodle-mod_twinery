# Twinery activity

## Description

This activity allows you to implement single-file .html apps from https://twinery.org/ as a activity in a course.

## How to implement Grading

To pass grades back to Moodle, your twinery must call

~~~
<script>
set backToMoodle to {
    type:"twine_result",
    score:23,
    feedback: "You are awesome"
}
window.parent.postMessage(State.variables.backToMoodle);
</script>
~~~

Grading is not secured right now, so in theory, tech-savvy students can easily give themselves whatever grade they want, using JS.