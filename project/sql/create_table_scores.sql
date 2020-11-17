CREATE TABLE Scores
(
    id      int auto_increment,
    user_id int,
    score   int default 0,
    created TIMESTAMP default current_timestamp,
    primary key (id),
    foreign key (user_id) references Users (id)
)