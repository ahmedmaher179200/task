## database design
* ```questions``` table uses a morph relationship, allowing it to accommodate new features that require questions, such as homework or exercises
* ```courses``` table has a many-to-many relationship with ```students (users)``` table, enabling students to enroll in multiple courses.
* ```courses``` table has one-to-may relation with ```exams``` table
* ```exams``` table has one-to-many relation with ```questions```
* ```questions``` table has a one-to-many relationship with ```answers``` table. Each answer is associated with a specific exam. The answers table includes a boolean column named ```is_answer```. If ```is_answer = true```, it indicates the correct answer. Otherwise, it represents the options available for the student to choose from, applicable to question types such as multiple-choice or true/false.
* ```students (users)``` table has many-to-many relationship with the ```exams``` table in ```user_exam``` pivot table
* ```user_exam``` table has one-to-many relationship with the ```user_exam_timeline``` table. This setup supports pausing and resuming exams, as the timeline table tracks the time elapsed between each pause and resume, facilitating precise time tracking.



## Why did you choose a specific relationship (e.g., One-to-Many) between tables X and Y?
The ```questions``` table uses a morph relationship, allowing it to accommodate new features that require questions, such as homework or exercises


## If the dataset grows significantly, how would you optimize the database for better performance?
I can optimize the database by adding indexes and implementing eager loading.
