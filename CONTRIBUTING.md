# Contribute to PetWatcher-API

First of all, thank you very much for considering supporting the development process of PetWatcher-API.
Please have a look at the following document which will help you understand how the project and all workflows are set up.

## GitFlow workflow
PetWatcher-API recently switched to the [GitFlow workflow](https://github.com/nvie/gitflow) (if you want to find out more
about the workflow, [Atlassian provides a great overview](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow))
which means that the repository is structured into the following branches:

| Branch | Explanation |
|-|-|
| master | The _master_ branch stores the official release history |
| develop | The _develop_ branch serves as an integration branch for features |
| feature/* | Each new feature should reside in its own branch. Feature branches use _develop_ as their parent branch and get merged back into _develop_ when completed. Features should never directly interact with _master_. |
| bugfix/* hotfix/* | Each bugfix should reside in its own branch. Bugfix branches use _develop_ as their parent branch and get merged back into _develop_ when completed. Bugfixes should never directly interact with _master_.<br><br>The only exception for interacting with _master_ are hotfixes, which should reside in a _hotfix/*_ branch that gets merged into _master_ when completed. These must, however, be of a very critical nature to skip the _develop_ branch. |
> Some explanations from the table above originate from [Atlassian's GitFlow workflow tutorial](https://www.atlassian.com/git/tutorials/comparing-workflows/gitflow-workflow) and have been slightly adapted.

## Contributing using Pull Requests
To actually contribute a feature, bugfix or even a hotfix, please follow these steps:
1. Fork the PetWatcher-API repository
2. Create a new branch according to the branching strategy from above
3. Send a pull request to the **develop** branch

To ensure that each feature can be properly tested and reviewed, please create a separate branch for every new feature or bugfix.

## Style Guide
PetWatcher-API adheres to the [PSR-1 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-1-basic-coding-standard.md)
as well as the [PSR-12 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-12-extended-coding-style-guide.md).
All pull requests must therefore also adhere to them. If not, the automatically triggered builds will fail, and the pull
request cannot be completed until all styling errors have been fixed.

## Unit Testing
All pull requests must be accompanied by passing unit tests and at the very least 80% of code coverage. PetWatcher-API uses
[PHPUnit](https://github.com/sebastianbergmann/phpunit/ "Learn more about using PHPUnit") for testing.
