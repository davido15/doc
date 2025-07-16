-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 13, 2025 at 03:45 PM
-- Server version: 5.7.34
-- PHP Version: 8.0.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pdf_verfier`
--

-- --------------------------------------------------------

--
-- Table structure for table `business_verifications`
--

CREATE TABLE `business_verifications` (
  `id` int(11) NOT NULL,
  `embassy_id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `business_type` varchar(100) DEFAULT NULL,
  `business_address` text,
  `contact_person` varchar(255) DEFAULT NULL,
  `contact_email` varchar(255) NOT NULL,
  `contact_phone` varchar(50) NOT NULL,
  `verification_reason` text,
  `verification_code` int(11) NOT NULL,
  `status` enum('Pending','In Progress','Verified','Rejected') DEFAULT 'Pending',
  `notes` text,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `report_file` longtext
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `business_verifications`
--

INSERT INTO `business_verifications` (`id`, `embassy_id`, `business_name`, `business_type`, `business_address`, `contact_person`, `contact_email`, `contact_phone`, `verification_reason`, `verification_code`, `status`, `notes`, `verified_by`, `verified_at`, `created_at`, `updated_at`, `report_file`) VALUES
(2, 2, 'StackSuite', 'NA', 'NA', 'NA', 'daviddors12@gmail.com', '+233246914600', 'we want to check if this exist', 48771796, 'Verified', 'good', 11, '2025-07-06 17:25:10', '2025-07-06 17:19:52', '2025-07-06 17:25:10', 'JVBERi0xLjMKMyAwIG9iago8PC9UeXBlIC9QYWdlCi9QYXJlbnQgMSAwIFIKL1Jlc291cmNlcyAyIDAgUgovQ29udGVudHMgNCAwIFI+PgplbmRvYmoKNCAwIG9iago8PC9GaWx0ZXIgL0ZsYXRlRGVjb2RlIC9MZW5ndGggNzkwPj4Kc3RyZWFtCnichddNT9swGMDxO5/CRzjkwW+P7XADMTQhbavUTrtwydowuvUFhSLEt5+T2LVjxNNjo+Yf55cnaSrZ/RkHtOzt7GbBLu8EExI4Z4tH9mXRb5KyBlkzWyNYyxYrdj5vts+blt00u39sfmgO7bbdHS7Y4m/Y4/JOlhElQPgGKpA4NK6Xy/3r7sC+7jertrti9/unHbvdt1km7CM11Hyyz/fX7e9+HyGVRmNdzT/sZWoDOB7puEI2a7v1fnXFJJdYcVNxwQ774yfFJ6fgHXg6BelAITNowSgmhAJVs2rc2LVsztKBNQdthgPf+uPGpNACrD4GrE+7MiC07q9DXmhflt36+bDe72JICQFOnFqJBoN56Ho7wD2c/5zfPlzElpYIktMt7T9ynbdumk2zW7ZlLF73j2jj/JBoflqEGOrHa2NLupAh6VJn0TW7l8e2K93oxYxuqSLQD58tvahG9EoNFFaBkzESUDiHWpMo6FS84Y4orkQJGQol68z9peveSxJ6KQNJ1qj82aAoSahGIMkayP04WT0lQSvBcJoEEbScktQFScyQJKlzvfjGfq0PT6uueWs2Bc2JJY002Zr8tEhX0JCNSJMaKHxWYkGj+200jbT9+eY0kpc0IUPSpM7sx5zNmvf84R5d6PWMLilU9V80H2CoSIRJEeTCgSthBPY/DhSMrh1YNYXRJUzIUDBZh54ZekmDTdaqhPPLLO8nMhJssoh2vU09tdHOAhraxp+wmg6NKocmZkib1Pl8aE6sZ4RJoUr5L6brHWDISIRJEe2wzh7gAcZvk4KG0bIvJRjrXxZKmJAhYVKHgKHXM8KkUKWkAK1LGCoSYVJEW+dfkEwBozi4ExPT38n1CZiQIWFSh4Ch1zPCpJB/0NryRiITkSUlhnmR5bxwCShJFuUQVDEvqmQJGYol63zyAnNiMYNJVjEWpClNqEQwyRIouN/EpybKahCWNvGvlZxPTcoHb8yQJqnzicmJxYwmqdI/WEx5/5CNiJIaKJRHPF7i4eVXMTf94+T8y6Nhvl+P/0RmzZ+WHSf0P1dB600KZW5kc3RyZWFtCmVuZG9iagoxIDAgb2JqCjw8L1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUiBdCi9Db3VudCAxCi9NZWRpYUJveCBbMCAwIDU5NS4yOCA4NDEuODldCj4+CmVuZG9iago1IDAgb2JqCjw8L1R5cGUgL0ZvbnQKL0Jhc2VGb250IC9IZWx2ZXRpY2EtQm9sZAovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjYgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYQovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjcgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYS1PYmxpcXVlCi9TdWJ0eXBlIC9UeXBlMQovRW5jb2RpbmcgL1dpbkFuc2lFbmNvZGluZwo+PgplbmRvYmoKMiAwIG9iago8PAovUHJvY1NldCBbL1BERiAvVGV4dCAvSW1hZ2VCIC9JbWFnZUMgL0ltYWdlSV0KL0ZvbnQgPDwKL0YxIDUgMCBSCi9GMiA2IDAgUgovRjMgNyAwIFIKPj4KL1hPYmplY3QgPDwKPj4KPj4KZW5kb2JqCjggMCBvYmoKPDwKL1Byb2R1Y2VyIChQeUZQREYgMS43LjIgaHR0cDovL3B5ZnBkZi5nb29nbGVjb2RlLmNvbS8pCi9DcmVhdGlvbkRhdGUgKEQ6MjAyNTA3MDYxNzEzMTcpCj4+CmVuZG9iago5IDAgb2JqCjw8Ci9UeXBlIC9DYXRhbG9nCi9QYWdlcyAxIDAgUgovT3BlbkFjdGlvbiBbMyAwIFIgL0ZpdEggbnVsbF0KL1BhZ2VMYXlvdXQgL09uZUNvbHVtbgo+PgplbmRvYmoKeHJlZgowIDEwCjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDk0NyAwMDAwMCBuIAowMDAwMDAxMzM1IDAwMDAwIG4gCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA4NyAwMDAwMCBuIAowMDAwMDAxMDM0IDAwMDAwIG4gCjAwMDAwMDExMzUgMDAwMDAgbiAKMDAwMDAwMTIzMSAwMDAwMCBuIAowMDAwMDAxNDU5IDAwMDAwIG4gCjAwMDAwMDE1NjggMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSAxMAovUm9vdCA5IDAgUgovSW5mbyA4IDAgUgo+PgpzdGFydHhyZWYKMTY3MQolJUVPRgo='),
(3, 2, 'testbusiness', 'NA', 'NA', 'NA', 'daviddors12@gmail.com', '+23326850370', 'i want to check if they are genuine', 63547819, 'Verified', '', 11, '2025-07-06 17:29:44', '2025-07-06 17:28:50', '2025-07-06 17:29:44', 'JVBERi0xLjMKMyAwIG9iago8PC9UeXBlIC9QYWdlCi9QYXJlbnQgMSAwIFIKL1Jlc291cmNlcyAyIDAgUgovQ29udGVudHMgNCAwIFI+PgplbmRvYmoKNCAwIG9iago8PC9GaWx0ZXIgL0ZsYXRlRGVjb2RlIC9MZW5ndGggNzkwPj4Kc3RyZWFtCnichddNT9swGMDxO5/CRzjkwW+P7XADMTQhbavUTrtwydowuvUFhSLEt5+T2LVjxNNjo+Yf55cnaSrZ/RkHtOzt7GbBLu8EExI4Z4tH9mXRb5KyBlkzWyNYyxYrdj5vts+blt00u39sfmgO7bbdHS7Y4m/Y4/JOlhElQPgGKpA4NK6Xy/3r7sC+7jertrti9/unHbvdt1km7CM11Hyyz/fX7e9+HyGVRmNdzT/sZWoDOB7puEI2a7v1fnXFJJdYcVNxwQ774yfFJ6fgHXg6BelAITNowSgmhAJVs2rc2LVsztKBNQdthgPf+uPGpNACrD4GrE+7MiC07q9DXmhflt36+bDe72JICQFOnFqJBoN56Ho7wD2c/5zfPlzElpYIktMt7T9ynbdumk2zW7ZlLF73j2jj/JBoflqEGOrHa2NLupAh6VJn0TW7l8e2K93oxYxuqSLQD58tvahG9EoNFFaBkzESUDiHWpMo6FS84Y4orkQJGQol68z9peveSxJ6KQNJ1qj82aAoSahGIMkayP04WT0lQSvBcJoEEbScktQFScyQJKlzvfjGfq0PT6uueWs2Bc2JJY002Zr8tEhX0JCNSJMaKHxWYkGj+200jbT9+eY0kpc0IUPSpM7sx5zNmvf84R5d6PWMLilU9V80H2CoSIRJEeTCgSthBPY/DhSMrh1YNYXRJUzIUDBZh54ZekmDTdaqhPPLLO8nMhJssoh2vU09tdHOAhraxp+wmg6NKocmZkib1Pl8aE6sZ4RJoUr5L6brHWDISIRJEe2wzh7gAcZvk4KG0bIvJRjrXxZKmJAhYVKHgKHXM8KkUKWkAK1LGCoSYVJEW+dfkEwBozi4ExPT38n1CZiQIWFSh4Ch1zPCpJB/0NryRiITkSUlhnmR5bxwCShJFuUQVDEvqmQJGYol63zyAnNiMYNJVjEWpClNqEQwyRIouN/EpybKahCWNvGvlZxPTcoHb8yQJqnzicmJxYwmqdI/WEx5/5CNiJIaKJRHPF7i4eVXMTf94+T8y6Nhvl+P/0RmzZ+WHSf0P1dB600KZW5kc3RyZWFtCmVuZG9iagoxIDAgb2JqCjw8L1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUiBdCi9Db3VudCAxCi9NZWRpYUJveCBbMCAwIDU5NS4yOCA4NDEuODldCj4+CmVuZG9iago1IDAgb2JqCjw8L1R5cGUgL0ZvbnQKL0Jhc2VGb250IC9IZWx2ZXRpY2EtQm9sZAovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjYgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYQovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjcgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYS1PYmxpcXVlCi9TdWJ0eXBlIC9UeXBlMQovRW5jb2RpbmcgL1dpbkFuc2lFbmNvZGluZwo+PgplbmRvYmoKMiAwIG9iago8PAovUHJvY1NldCBbL1BERiAvVGV4dCAvSW1hZ2VCIC9JbWFnZUMgL0ltYWdlSV0KL0ZvbnQgPDwKL0YxIDUgMCBSCi9GMiA2IDAgUgovRjMgNyAwIFIKPj4KL1hPYmplY3QgPDwKPj4KPj4KZW5kb2JqCjggMCBvYmoKPDwKL1Byb2R1Y2VyIChQeUZQREYgMS43LjIgaHR0cDovL3B5ZnBkZi5nb29nbGVjb2RlLmNvbS8pCi9DcmVhdGlvbkRhdGUgKEQ6MjAyNTA3MDYxNzEzMTcpCj4+CmVuZG9iago5IDAgb2JqCjw8Ci9UeXBlIC9DYXRhbG9nCi9QYWdlcyAxIDAgUgovT3BlbkFjdGlvbiBbMyAwIFIgL0ZpdEggbnVsbF0KL1BhZ2VMYXlvdXQgL09uZUNvbHVtbgo+PgplbmRvYmoKeHJlZgowIDEwCjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDk0NyAwMDAwMCBuIAowMDAwMDAxMzM1IDAwMDAwIG4gCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA4NyAwMDAwMCBuIAowMDAwMDAxMDM0IDAwMDAwIG4gCjAwMDAwMDExMzUgMDAwMDAgbiAKMDAwMDAwMTIzMSAwMDAwMCBuIAowMDAwMDAxNDU5IDAwMDAwIG4gCjAwMDAwMDE1NjggMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSAxMAovUm9vdCA5IDAgUgovSW5mbyA4IDAgUgo+PgpzdGFydHhyZWYKMTY3MQolJUVPRgo='),
(4, 2, 'StackSuite', 'NA', 'NA', 'NA', 'daviddors12@gmail.com', '+233246914600', 'KKK', 31281938, 'Verified', 'fff', 11, '2025-07-13 15:39:39', '2025-07-13 15:34:11', '2025-07-13 15:39:39', 'JVBERi0xLjMKMyAwIG9iago8PC9UeXBlIC9QYWdlCi9QYXJlbnQgMSAwIFIKL1Jlc291cmNlcyAyIDAgUgovQ29udGVudHMgNCAwIFI+PgplbmRvYmoKNCAwIG9iago8PC9GaWx0ZXIgL0ZsYXRlRGVjb2RlIC9MZW5ndGggNzkwPj4Kc3RyZWFtCnichddNT9swGMDxO5/CRzjkwW+P7XADMTQhbavUTrtwydowuvUFhSLEt5+T2LVjxNNjo+Yf55cnaSrZ/RkHtOzt7GbBLu8EExI4Z4tH9mXRb5KyBlkzWyNYyxYrdj5vts+blt00u39sfmgO7bbdHS7Y4m/Y4/JOlhElQPgGKpA4NK6Xy/3r7sC+7jertrti9/unHbvdt1km7CM11Hyyz/fX7e9+HyGVRmNdzT/sZWoDOB7puEI2a7v1fnXFJJdYcVNxwQ774yfFJ6fgHXg6BelAITNowSgmhAJVs2rc2LVsztKBNQdthgPf+uPGpNACrD4GrE+7MiC07q9DXmhflt36+bDe72JICQFOnFqJBoN56Ho7wD2c/5zfPlzElpYIktMt7T9ynbdumk2zW7ZlLF73j2jj/JBoflqEGOrHa2NLupAh6VJn0TW7l8e2K93oxYxuqSLQD58tvahG9EoNFFaBkzESUDiHWpMo6FS84Y4orkQJGQol68z9peveSxJ6KQNJ1qj82aAoSahGIMkayP04WT0lQSvBcJoEEbScktQFScyQJKlzvfjGfq0PT6uueWs2Bc2JJY002Zr8tEhX0JCNSJMaKHxWYkGj+200jbT9+eY0kpc0IUPSpM7sx5zNmvf84R5d6PWMLilU9V80H2CoSIRJEeTCgSthBPY/DhSMrh1YNYXRJUzIUDBZh54ZekmDTdaqhPPLLO8nMhJssoh2vU09tdHOAhraxp+wmg6NKocmZkib1Pl8aE6sZ4RJoUr5L6brHWDISIRJEe2wzh7gAcZvk4KG0bIvJRjrXxZKmJAhYVKHgKHXM8KkUKWkAK1LGCoSYVJEW+dfkEwBozi4ExPT38n1CZiQIWFSh4Ch1zPCpJB/0NryRiITkSUlhnmR5bxwCShJFuUQVDEvqmQJGYol63zyAnNiMYNJVjEWpClNqEQwyRIouN/EpybKahCWNvGvlZxPTcoHb8yQJqnzicmJxYwmqdI/WEx5/5CNiJIaKJRHPF7i4eVXMTf94+T8y6Nhvl+P/0RmzZ+WHSf0P1dB600KZW5kc3RyZWFtCmVuZG9iagoxIDAgb2JqCjw8L1R5cGUgL1BhZ2VzCi9LaWRzIFszIDAgUiBdCi9Db3VudCAxCi9NZWRpYUJveCBbMCAwIDU5NS4yOCA4NDEuODldCj4+CmVuZG9iago1IDAgb2JqCjw8L1R5cGUgL0ZvbnQKL0Jhc2VGb250IC9IZWx2ZXRpY2EtQm9sZAovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjYgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYQovU3VidHlwZSAvVHlwZTEKL0VuY29kaW5nIC9XaW5BbnNpRW5jb2RpbmcKPj4KZW5kb2JqCjcgMCBvYmoKPDwvVHlwZSAvRm9udAovQmFzZUZvbnQgL0hlbHZldGljYS1PYmxpcXVlCi9TdWJ0eXBlIC9UeXBlMQovRW5jb2RpbmcgL1dpbkFuc2lFbmNvZGluZwo+PgplbmRvYmoKMiAwIG9iago8PAovUHJvY1NldCBbL1BERiAvVGV4dCAvSW1hZ2VCIC9JbWFnZUMgL0ltYWdlSV0KL0ZvbnQgPDwKL0YxIDUgMCBSCi9GMiA2IDAgUgovRjMgNyAwIFIKPj4KL1hPYmplY3QgPDwKPj4KPj4KZW5kb2JqCjggMCBvYmoKPDwKL1Byb2R1Y2VyIChQeUZQREYgMS43LjIgaHR0cDovL3B5ZnBkZi5nb29nbGVjb2RlLmNvbS8pCi9DcmVhdGlvbkRhdGUgKEQ6MjAyNTA3MDYxNzEzMTcpCj4+CmVuZG9iago5IDAgb2JqCjw8Ci9UeXBlIC9DYXRhbG9nCi9QYWdlcyAxIDAgUgovT3BlbkFjdGlvbiBbMyAwIFIgL0ZpdEggbnVsbF0KL1BhZ2VMYXlvdXQgL09uZUNvbHVtbgo+PgplbmRvYmoKeHJlZgowIDEwCjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDk0NyAwMDAwMCBuIAowMDAwMDAxMzM1IDAwMDAwIG4gCjAwMDAwMDAwMDkgMDAwMDAgbiAKMDAwMDAwMDA4NyAwMDAwMCBuIAowMDAwMDAxMDM0IDAwMDAwIG4gCjAwMDAwMDExMzUgMDAwMDAgbiAKMDAwMDAwMTIzMSAwMDAwMCBuIAowMDAwMDAxNDU5IDAwMDAwIG4gCjAwMDAwMDE1NjggMDAwMDAgbiAKdHJhaWxlcgo8PAovU2l6ZSAxMAovUm9vdCA5IDAgUgovSW5mbyA4IDAgUgo+PgpzdGFydHhyZWYKMTY3MQolJUVPRgo=');

-- --------------------------------------------------------

--
-- Table structure for table `document_verifications`
--

CREATE TABLE `document_verifications` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `action` enum('upload','download','integrity_pass','integrity_fail') NOT NULL,
  `timestamp` int(11) NOT NULL,
  `digital_signature` varchar(255) NOT NULL,
  `salt` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `document_verifications`
--

INSERT INTO `document_verifications` (`id`, `document_id`, `email`, `action`, `timestamp`, `digital_signature`, `salt`) VALUES
(50, 39, 'testbank@bank.com', 'upload', 1751818593, 'd6cd83092a828c16410ad4b554f77e6f1af0a0942e0b9e024a0bc7d546182807', '46ae2037dd80f961fb6826101370e08d'),
(51, 39, 'testembassy@embassy.com', 'download', 1751818666, '2efee1a9eb9cebd69f819664ca41b79be7e97ec75642d73727bee0d9547f1658', '445b9b2c250d57dfaf4189edbaac7b19'),
(52, 39, 'testembassy@embassy.com', 'integrity_fail', 1751818677, 'FAIL', '43d948c84e6978c256fd6c3bbe721fd0be614027da2ee71d3579df66911af1c9'),
(53, 39, 'testembassy@embassy.com', 'integrity_fail', 1751818948, 'FAIL', '43d948c84e6978c256fd6c3bbe721fd0be614027da2ee71d3579df66911af1c9'),
(54, 40, 'testbank@bank.com', 'upload', 1751819131, '0ef99d4df020b0bdf6d1f2d577d955705145f1eb0effb52acd0d807b434d38f0', 'c701587349d59dd8c8c19b4676b3d987'),
(55, 40, 'testembassy@embassy.com', 'integrity_fail', 1751819217, 'FAIL', '99e0ebcd816cf881850357ad1b626383e5c7fc119667dfc7353a330179e5cc62'),
(56, 40, 'testembassy@embassy.com', 'download', 1751819234, 'd2c703531e3477b0f32f6d7e2284e382644b52565579cc260af7e6bafa2519c1', 'ed4c579bfe3b0f442f0a21bdefbb4bc3'),
(57, 40, 'testembassy@embassy.com', 'integrity_fail', 1751819681, 'FAIL', 'c251bb690d40ca95a94163fed208ea57a317e15fb47accdf582bf8bfb0b6cfb1'),
(58, 40, 'testembassy@embassy.com', 'integrity_fail', 1751819827, '09ffc19ea50642d43eb65dfbe11f71fb81d1c117c37203257efa2fe4c4386483', 'c251bb690d40ca95a94163fed208ea57a317e15fb47accdf582bf8bfb0b6cfb1'),
(60, 41, 'testbank@bank.com', 'upload', 1751821119, 'aa5a1f5d2cd9168a62ef0b7dc1e78831888099e58c1cb6b5f354b2116d268aaa', '0a9344466b9cb6c8f9ff596669267977'),
(62, 41, 'testembassy@embassy.com', 'download', 1751821337, '29abbe97284effe153ac01978a5ddf1268573608a927c8fdbd22bcdcc14fbb9d', '5ce01f5c3ffc093e71fee12d92235f6f'),
(63, 41, 'testembassy@embassy.com', 'integrity_pass', 1751821356, '20186ce9d5664bd3d046cfa5a92a2a4352e2daf9feea8c4359efe86c696ed321', '20186ce9d5664bd3d046cfa5a92a2a4352e2daf9feea8c4359efe86c696ed321'),
(64, 42, 'testbank@bank.com', 'upload', 1751821657, '13ff1a5a1a7510c2e6ecc60b0b9e0c35b8ff8a9aa8451422b3c4c3b8ced94171', 'e4e20a565d3f7d0e0b18da05d2a4aaf9'),
(65, 42, 'testembassy@embassy.com', 'download', 1751821750, '1e0de24b9c219853c07654ca47f6b8632746e2b81b0928ad081a21645ff4e886', '7672734855dcde6f4c9bb87c7252bf0f'),
(66, 43, 'testbank@bank.com', 'upload', 1751822181, '8401657537a269125cf36b6588df84b60c1a76088dd6c99dc16c3e99bfbaf9e3', '6056e404902d366d19bd845d92244d5e'),
(67, 43, 'testembassy@embassy.com', 'download', 1751822278, 'c6ccdbffaf54b0390aa2584bc777a663ab64aa54ff3d5eaf3de58ad22a4c3979', 'deb6bc78fba0d2867bc83fa35aaaecf0'),
(68, 43, 'testembassy@embassy.com', 'integrity_pass', 1751822307, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867'),
(69, 44, 'testbank@bank.com', 'upload', 1751822811, 'd938c375bb44e20072b6fe83dd1f791d981ae1541be6945bebe7d8ccd895df2a', '75d8ccb670538b348c0072f3204b32f8'),
(70, 44, 'testembassy@embassy.com', 'download', 1751822882, '2d64c41baddeb8d6f2e4d1a3e6bccc61b98935b9f82efc36e2139611dddd538d', 'c5bb06870ef9049ea3fa7dd77cad6617'),
(71, 44, 'testembassy@embassy.com', 'integrity_pass', 1751822897, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867'),
(73, 45, 'testbank@bank.com', 'upload', 1752411604, 'de8475a4be8f1f3caeaf0e17d891e43e05c8a4782211762e840f4b4146b855e2', '119b9a091e33a675a2ec323503b216da'),
(75, 46, 'testbank@bank.com', 'upload', 1752420198, '7007f7c4fad86125ebf7203774648a20cd78834e20d338582e5e411f49cd06b0', '9ed212b24cc40e9ab4b6f4a618569be3');

-- --------------------------------------------------------

--
-- Table structure for table `download_logs`
--

CREATE TABLE `download_logs` (
  `id` int(11) NOT NULL,
  `file_id` int(11) NOT NULL,
  `uploader_email` varchar(255) NOT NULL,
  `downloader_email` varchar(255) NOT NULL,
  `download_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `download_logs`
--

INSERT INTO `download_logs` (`id`, `file_id`, `uploader_email`, `downloader_email`, `download_time`) VALUES
(11, 39, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 16:17:46'),
(12, 40, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 16:27:14'),
(13, 41, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 17:02:17'),
(14, 42, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 17:09:10'),
(15, 43, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 17:17:58'),
(16, 44, 'daviddors12@gmail.com', 'testembassy@embassy.com', '2025-07-06 17:28:02');

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `attempt_time` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`id`, `name`, `domain`, `status`, `created_at`) VALUES
(1, 'AccessBank', 'Bank', 'active', '2025-06-14 14:45:20'),
(2, 'TurkishEmbassy', 'Embassy', 'active', '2025-06-14 14:45:20'),
(3, 'USEmbassy', 'Embassy', 'active', '2025-07-06 11:23:35'),
(99, 'AllEmbassy', 'AllEmbassy', 'active', '2025-07-05 12:36:12');

-- --------------------------------------------------------

--
-- Table structure for table `otps`
--

CREATE TABLE `otps` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `expires_at` datetime NOT NULL,
  `type` varchar(50) DEFAULT 'login',
  `is_used` tinyint(1) DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `otps`
--

INSERT INTO `otps` (`id`, `email`, `code`, `expires_at`, `type`, `is_used`, `created_at`) VALUES
(71, 'daviddors12@gmail.com', '128041', '2025-07-06 16:25:25', 'login', 1, '2025-07-06 16:15:25'),
(72, 'daviddors12@gmail.com', '646043', '2025-07-06 16:27:07', 'login', 1, '2025-07-06 16:17:07'),
(73, 'daviddors12@gmail.com', '633165', '2025-07-06 16:33:42', 'login', 1, '2025-07-06 16:23:42'),
(74, 'daviddors12@gmail.com', '205473', '2025-07-06 16:36:00', 'login', 1, '2025-07-06 16:26:00'),
(75, 'daviddors12@gmail.com', '509222', '2025-07-06 16:48:45', 'login', 0, '2025-07-06 16:38:45'),
(76, 'daviddors12@gmail.com', '913743', '2025-07-06 16:56:50', 'login', 0, '2025-07-06 16:46:50'),
(77, 'daviddors12@gmail.com', '641984', '2025-07-06 16:59:40', 'login', 1, '2025-07-06 16:49:40'),
(78, 'daviddors12@gmail.com', '170370', '2025-07-06 17:07:41', 'login', 1, '2025-07-06 16:57:41'),
(79, 'daviddors12@gmail.com', '635432', '2025-07-06 17:10:46', 'login', 1, '2025-07-06 17:00:46'),
(80, 'daviddors12@gmail.com', '998861', '2025-07-06 17:16:17', 'login', 1, '2025-07-06 17:06:17'),
(81, 'daviddors12@gmail.com', '547086', '2025-07-06 17:18:15', 'login', 1, '2025-07-06 17:08:15'),
(82, 'daviddors12@gmail.com', '598959', '2025-07-06 17:20:46', 'login', 1, '2025-07-06 17:10:46'),
(83, 'daviddors12@gmail.com', '208834', '2025-07-06 17:25:18', 'login', 1, '2025-07-06 17:15:18'),
(84, 'daviddors12@gmail.com', '476496', '2025-07-06 17:27:00', 'login', 1, '2025-07-06 17:17:00'),
(85, 'daviddors12@gmail.com', '787567', '2025-07-06 17:30:50', 'login', 1, '2025-07-06 17:20:50'),
(86, 'daviddors12@gmail.com', '468602', '2025-07-06 17:34:12', 'login', 1, '2025-07-06 17:24:12'),
(87, 'daviddors12@gmail.com', '707831', '2025-07-06 17:36:08', 'login', 1, '2025-07-06 17:26:08'),
(88, 'daviddors12@gmail.com', '529706', '2025-07-06 17:37:24', 'login', 1, '2025-07-06 17:27:24'),
(89, 'daviddors12@gmail.com', '152177', '2025-07-06 17:39:10', 'login', 1, '2025-07-06 17:29:10'),
(90, 'daviddors12@gmail.com', '121882', '2025-07-13 11:07:53', 'login', 0, '2025-07-13 10:57:53'),
(91, 'daviddors12@gmail.com', '630697', '2025-07-13 11:10:51', 'login', 1, '2025-07-13 11:00:51'),
(92, 'daviddors12@gmail.com', '992470', '2025-07-13 11:12:17', 'login', 1, '2025-07-13 11:02:17'),
(93, 'daviddors12@gmail.com', '600343', '2025-07-13 11:13:16', 'login', 0, '2025-07-13 11:03:16'),
(94, 'daviddors12@gmail.com', '860677', '2025-07-13 11:13:23', 'login', 1, '2025-07-13 11:03:23'),
(95, 'daviddors12@gmail.com', '121893', '2025-07-13 11:17:02', 'login', 0, '2025-07-13 11:07:02'),
(96, 'daviddors12@gmail.com', '998363', '2025-07-13 11:19:45', 'login', 1, '2025-07-13 11:09:45'),
(97, 'daviddors12@gmail.com', '103754', '2025-07-13 12:11:01', 'login', 0, '2025-07-13 12:01:01'),
(98, 'daviddors12@gmail.com', '964728', '2025-07-13 12:31:18', 'login', 1, '2025-07-13 12:21:18'),
(99, 'daviddors12@gmail.com', '446282', '2025-07-13 12:39:39', 'login', 0, '2025-07-13 12:29:39'),
(100, 'daviddors12@gmail.com', '542441', '2025-07-13 12:40:06', 'login', 1, '2025-07-13 12:30:06'),
(101, 'daviddors12@gmail.com', '678751', '2025-07-13 12:48:18', 'login', 0, '2025-07-13 12:38:18'),
(102, 'daviddors12@gmail.com', '211287', '2025-07-13 12:53:20', 'login', 0, '2025-07-13 12:43:20'),
(103, 'daviddors12@gmail.com', '944696', '2025-07-13 12:54:16', 'login', 1, '2025-07-13 12:44:16'),
(104, 'daviddors12@gmail.com', '360890', '2025-07-13 13:17:51', 'login', 0, '2025-07-13 13:07:51'),
(105, 'daviddors12@gmail.com', '093926', '2025-07-13 13:21:45', 'login', 1, '2025-07-13 13:11:45'),
(106, 'daviddors12@gmail.com', '678393', '2025-07-13 14:53:19', 'login', 1, '2025-07-13 14:43:19'),
(107, 'daviddors12@gmail.com', '193954', '2025-07-13 14:59:14', 'login', 1, '2025-07-13 14:49:14'),
(108, 'daviddors12@gmail.com', '492894', '2025-07-13 15:01:11', 'login', 1, '2025-07-13 14:51:11'),
(109, 'daviddors12@gmail.com', '483561', '2025-07-13 15:03:28', 'login', 1, '2025-07-13 14:53:28'),
(110, 'daviddors12@gmail.com', '611556', '2025-07-13 15:04:41', 'login', 1, '2025-07-13 14:54:41'),
(111, 'daviddors12@gmail.com', '275930', '2025-07-13 15:31:06', 'login', 0, '2025-07-13 15:21:06'),
(112, 'daviddors12@gmail.com', '456716', '2025-07-13 15:32:37', 'login', 1, '2025-07-13 15:22:37'),
(113, 'daviddors12@gmail.com', '996190', '2025-07-13 15:33:56', 'login', 1, '2025-07-13 15:23:56'),
(114, 'daviddors12@gmail.com', '485844', '2025-07-13 15:45:42', 'login', 1, '2025-07-13 15:35:42');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expiry` datetime NOT NULL,
  `used` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pdf_requests`
--

CREATE TABLE `pdf_requests` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_path` text,
  `verification_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phonenumber` varchar(50) NOT NULL,
  `date` date NOT NULL,
  `file_url` longtext NOT NULL,
  `verification_code` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `Status` enum('With Bank','With Embassy','Expired','With All Embassy') NOT NULL DEFAULT 'With Bank',
  `embassy_id` int(11) DEFAULT NULL,
  `file_hash` varchar(255) DEFAULT NULL,
  `requesting_for` enum('myself','someone_else') DEFAULT NULL,
  `beneficiary_name` varchar(255) DEFAULT NULL,
  `beneficiary_dob` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `uploads`
--

INSERT INTO `uploads` (`id`, `organization_id`, `user_id`, `name`, `email`, `phonenumber`, `date`, `file_url`, `verification_code`, `uploaded_at`, `Status`, `embassy_id`, `file_hash`, `requesting_for`, `beneficiary_name`, `beneficiary_dob`) VALUES
(39, 2, 21, 'John Doe', 'daviddors12@gmail.com', '0246914600', '2025-07-06', 'grp_686aa16015f0d8.31969236_DavidDornyoh.pdf', 78056914, '2025-07-06 16:16:33', 'With Embassy', 2, 'b134cd9188f21d77541615219f85479e439ad8e5685ac9cd29a0ef1c7fe22bfd', 'someone_else', 'David', '2025-07-06'),
(40, 2, 21, 'ghh', 'daviddors12@gmail.com', '6666', '2025-07-06', 'grp_686aa378dba840.49132625_GROUP9.pdf', 50362657, '2025-07-06 16:25:31', 'With Embassy', 2, '09ffc19ea50642d43eb65dfbe11f71fb81d1c117c37203257efa2fe4c4386483', 'someone_else', 'test', '2025-07-06'),
(41, 2, 21, 'John Doe', 'daviddors12@gmail.com', '26850370', '2025-07-06', 'grp_686aab3daa0128.30198346_test_bank_statement.pdf', 70836379, '2025-07-06 16:58:39', 'With Embassy', 2, '20186ce9d5664bd3d046cfa5a92a2a4352e2daf9feea8c4359efe86c696ed321', 'someone_else', 'David Dors', '2025-07-04'),
(42, 2, 21, 'John Doe', 'daviddors12@gmail.com', '26850370', '2025-07-06', 'grp_686aad58b25b55.62480429_test_bank_statement.pdf', 93791065, '2025-07-06 17:07:37', 'With Embassy', 2, '20186ce9d5664bd3d046cfa5a92a2a4352e2daf9feea8c4359efe86c696ed321', 'someone_else', 'David DOrs', '2025-07-06'),
(43, 2, 21, 'John Doe', 'daviddors12@gmail.com', '26850370', '2025-07-06', 'grp_686aaf6453e2d1.35841718_sample_bank_statement.pdf', 67278044, '2025-07-06 17:16:21', 'With Embassy', 2, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'someone_else', 'David Test', '2025-07-06'),
(44, 2, 21, 'John Does', 'daviddors12@gmail.com', '26850370', '2025-07-06', 'grp_686ab1da8f7e81.89297780_sample_bank_statement.pdf', 27666641, '2025-07-06 17:26:51', 'With Embassy', 2, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'someone_else', 'David DOrs', '2025-07-06'),
(45, 2, 21, 'Dornyoh David', 'daviddors12@gmail.com', '26850370', '2025-07-13', 'grp_6873add4041df7.96189517_sample_bank_statement.pdf', 72692834, '2025-07-13 13:00:04', 'With Embassy', 2, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'someone_else', 'David Test', '2025-07-13'),
(46, 2, 21, 'Dornyoh David', 'daviddors12@gmail.com', '26850370', '2025-07-13', 'grp_6873cf64565486.22835458_sample_bank_statement.pdf', 91017062, '2025-07-13 15:23:18', 'With Embassy', 2, 'c89c9b346157ca7ad68a75fec9a2869bbf7fdb9a09678139074b10a830132867', 'someone_else', 'David Test', '2025-07-13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `organization_id` int(11) NOT NULL DEFAULT '0',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT '0',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `organization_id`, `email`, `phone`, `password`, `is_verified`, `status`, `created_at`, `first_name`, `last_name`) VALUES
(1, 0, 'david@gmail.com', NULL, '$2y$10$yg3TY4niVZkzIP1ydmZNM.UXbBjwJIzXYBDfkAN2J2iewOycI6XL6', 0, 'active', '2025-06-13 13:21:25', 'david', 'Dornyoh'),
(2, 0, 'dg@gmail.com', NULL, '$2y$10$PAVurHiRUM3HqP1vnaIB6ef49TqVaORqpEbyj6GtcJozz9ZDqThh2', 0, 'active', '2025-06-13 13:22:33', 'fff', 'jjuj'),
(5, 0, 'daviddors@gmail.com', NULL, '$2y$10$NJGjubf2ltKzySrtgdneUujf4FYX5aE3povLUuDBVvjJSTWWC.t3W', 0, 'active', '2025-06-13 13:26:22', 'david', 'dornyoh'),
(11, 0, 'test@gmail.com', NULL, '$2y$10$iw.lMS7sqiezN9tyJwJWyuSUlrHh084AS3Q4InQ1VC2/jbFw8mZjS', 1, 'active', '2025-06-13 13:29:55', 'Dornyoh', 'David'),
(15, 0, 'test123@gmail.com', '0246914600', '$2y$10$HRit9QN7imxfy.ufB3DwKOBnkgp/KC15.giyP5Z79ca3p5WVF5A02', 0, 'active', '2025-06-13 15:08:20', 'debbb', 'ffvfv'),
(16, 0, 'daviddors12@gmail.com', '0246914600', '$2y$10$t0AU5lLbQTWujNHYxzAOr.rDKcT8j5urIEGDcHh9Q3r5.klHNSYU6', 1, 'active', '2025-06-13 15:09:55', 'Dornyoh', 'David'),
(17, 0, 'daviddors122@gmail.com', '26850370', '$2y$10$YomfFHFgjged.GWKrFovfO44NZZBznf85OzFNNPTcb65.99QtcZmG', 0, 'active', '2025-06-13 15:10:57', 'ggg', 'gggg'),
(18, 1, 'daviddors1234@gmail.com', '26850370f', '$2y$10$k6pr8Or1aSt4h.glLKnzgulayy5ceM37OavQYbi6Sp0/x0OpLAXKS', 0, 'active', '2025-06-13 15:11:34', 'Dornyoh', 'David'),
(19, 0, 'daviddors129@gmail.com', '268503701', '$2y$10$TZCDh20QWl/oEki0se93Ouygo85Mj6sVkZjoExeL9YGkhTSKk.wfa', 0, 'active', '2025-06-13 15:12:13', 'Dornyoh', 'David'),
(20, 0, 'dav@gmail.com', '0246914600', '$2y$10$kt74VYqlzZEteqR59tYyquacSUVbaKDv5XcNyI8SiWAVBY0GTYjWW', 0, 'active', '2025-06-13 15:22:46', 'cccc', 'ccccccc'),
(21, 1, 'testbank@bank.com', '0246914600', '$2y$10$TXTWdsEjvYDSrH4lV42h5ut1sxdCBCHqn7XfRXZPj1oH/mTWX9Sna', 1, 'active', '2025-06-15 13:38:44', 'test', 'bank'),
(22, 2, 'testembassy@embassy.com', '0246914600', '$2y$10$iaMfeUX6Of1gGjrGlbWf8e55UjR7Otkk4Xt4HCw4bxAoN6pr1GiP2', 1, 'active', '2025-06-15 13:39:55', 'test', 'embassy'),
(23, 2, 'testbank@gmail.com', '0246914600', '$2y$10$VxREQM7uyO.9pS0I1LgxE.5WwUNAWl2W/F/VV7aPKP9S3WT2dfiL.', 0, 'active', '2025-06-15 14:06:59', 'test', 'bankt'),
(24, 1, 'testbank@bank.om', NULL, 'y.UXbBjwJIzXYBDfkAN2J2iewOycI6XL6', 0, 'active', '2025-06-16 17:34:22', NULL, NULL),
(25, 0, 'test1@gmail.com', '012223', '$2y$10$hD0WjBRwnAl1zz/zW./ZteFUvhn62U.waUL6B2pXsuBMCK.4OQy2e', 0, 'active', '2025-06-16 20:19:12', 'tes', 'fff'),
(26, 0, 'daviddors17@gmail.com', '26850374', '$2y$10$zZH/IqAbD58N3ULmUAeXVOZ6OAPFo5cEnRM7pj.EKWDTNXPg1H4vC', 0, 'active', '2025-06-23 17:42:45', 'Dornyoh', 'David'),
(27, 0, 'daviddors18@gmail.com', '26850370', '$2y$10$X8BK4kIVB6SayE92TYt7mu3eUVZj2n3QBIAVJn6rpxWchxAksKeYW', 0, 'active', '2025-06-23 17:50:07', 'Dornyoh', 'David');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `business_verifications`
--
ALTER TABLE `business_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verification_code` (`verification_code`),
  ADD KEY `verified_by` (`verified_by`),
  ADD KEY `idx_business_verifications_embassy_id` (`embassy_id`),
  ADD KEY `idx_business_verifications_status` (`status`),
  ADD KEY `idx_business_verifications_verification_code` (`verification_code`);

--
-- Indexes for table `document_verifications`
--
ALTER TABLE `document_verifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `document_id` (`document_id`);

--
-- Indexes for table `download_logs`
--
ALTER TABLE `download_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `file_id` (`file_id`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `attempt_time` (`attempt_time`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `otps`
--
ALTER TABLE `otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_email_type` (`email`,`type`),
  ADD KEY `idx_expires_at` (`expires_at`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `email` (`email`),
  ADD KEY `expiry` (`expiry`);

--
-- Indexes for table `pdf_requests`
--
ALTER TABLE `pdf_requests`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `verification_code` (`verification_code`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `embassy_id` (`embassy_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `business_verifications`
--
ALTER TABLE `business_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `document_verifications`
--
ALTER TABLE `document_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=76;

--
-- AUTO_INCREMENT for table `download_logs`
--
ALTER TABLE `download_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `otps`
--
ALTER TABLE `otps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pdf_requests`
--
ALTER TABLE `pdf_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `business_verifications`
--
ALTER TABLE `business_verifications`
  ADD CONSTRAINT `business_verifications_ibfk_1` FOREIGN KEY (`embassy_id`) REFERENCES `organizations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `business_verifications_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `document_verifications`
--
ALTER TABLE `document_verifications`
  ADD CONSTRAINT `document_verifications_ibfk_1` FOREIGN KEY (`document_id`) REFERENCES `uploads` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `download_logs`
--
ALTER TABLE `download_logs`
  ADD CONSTRAINT `download_logs_ibfk_1` FOREIGN KEY (`file_id`) REFERENCES `uploads` (`id`);

--
-- Constraints for table `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_ibfk_1` FOREIGN KEY (`embassy_id`) REFERENCES `organizations` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
